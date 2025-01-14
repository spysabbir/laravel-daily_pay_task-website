<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Verification;
use App\Notifications\VerificationNotification;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Bonus;
use App\Notifications\BonusNotification;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\UserDevice;

class VerificationController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('verification.request') , only:['verificationRequest']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('verification.request.check') , only:['verificationRequestShow', 'verificationRequestStatusChange']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('verification.request.rejected'), only:['verificationRequestRejected']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('verification.request.approved') , only:['verificationRequestApproved']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('verification.request.delete') , only:['verificationRequestDelete']),
        ];
    }

    public function verificationRequest(Request $request)
    {
        if ($request->ajax()) {
            $query = Verification::where('status', 'Pending');

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $query->select('verifications.*')->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalVerificationsCount = (clone $query)->count();

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->editColumn('user_email', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->email . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">Check</button>
                    ';
                return $btn;
                })
                ->with([
                    'totalVerificationsCount' => $totalVerificationsCount,
                ])
                ->rawColumns(['user_name', 'user_email', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.verification.index');
    }

    public function verificationRequestShow(string $id)
    {
        $verification = Verification::where('id', $id)->first();

        $userIps = UserDevice::where('user_id', $verification->user_id)->groupBy('ip')->pluck('ip')->toArray();
        $sameIpUserIds = UserDevice::whereIn('ip', $userIps)
            ->where('user_id', '!=', $verification->user_id)
            ->groupBy('user_id')
            ->pluck('user_id')
            ->toArray();
        $sameIpUsers = User::whereIn('id', $sameIpUserIds)
            ->whereIn('status', ['Active', 'Blocked'])
            ->where('user_type', 'Frontend')
            ->get();

        return view('backend.verification.show', compact('verification', 'sameIpUsers'));
    }

    public function verificationRequestStatusChange(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'rejected_reason' => 'required_if:status,Rejected',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        $verification = Verification::findOrFail($id);
        $verification->update([
            'status' => $request->status,
            'rejected_reason' => $request->status == 'Rejected' ? $request->rejected_reason : NULL,
            'rejected_by' => $request->status == 'Rejected' ? auth()->user()->id : NULL,
            'rejected_at' => $request->status == 'Rejected' ? now() : NULL,
            'approved_by' => $request->status == 'Approved' ? auth()->user()->id : NULL,
            'approved_at' => $request->status == 'Approved' ? now() : NULL,
        ]);

        $user = User::findOrFail($verification->user_id);

        if ($request->status == 'Approved') {
            $user->update([
                'status' => 'Active',
            ]);

            $user = User::where('id', $verification->user_id)->first();
            $referralBonus = get_default_settings('referral_registration_bonus_amount');
            if ($user->referred_by) {
                $referrer = User::where('id', $user->referred_by)->first();
                $referrer->increment('withdraw_balance', $referralBonus);

                $referrerBonus = Bonus::create([
                    'user_id' => $referrer->id,
                    'bonus_by' => $user->id,
                    'type' => 'Referral Registration Bonus',
                    'amount' => $referralBonus,
                ]);

                $referrer->notify(new BonusNotification($referrerBonus));
            }
        }

        $user->notify(new VerificationNotification($verification));

        return response()->json([
            'status' => 200,
        ]);
    }

    public function verificationRequestRejected(Request $request)
    {
        if ($request->ajax()) {
            $query = Verification::where('status', 'Rejected');

            $query->select('verifications.*')->orderBy('rejected_at', 'desc');

            $rejectedData = $query->get();

            return DataTables::of($rejectedData)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->editColumn('user_email', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->email . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('rejected_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->rejectedBy->name . '</span>
                        ';
                })
                ->editColumn('rejected_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->rejected_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $deletePermission = auth()->user()->can('verification.request.delete');

                    $deleteBtn = $deletePermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>'
                        : '';

                    return $deleteBtn;
                })
                ->rawColumns(['user_name', 'user_email', 'created_at', 'rejected_by', 'rejected_at', 'action'])
                ->make(true);
        }

        return view('backend.verification.index');
    }

    public function verificationRequestApproved(Request $request)
    {
        if ($request->ajax()) {
            $query = Verification::where('status', 'Approved');

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $query->select('verifications.*')->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalVerificationsCount = (clone $query)->count();

            $approvedRequest = $query->get();

            return DataTables::of($approvedRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->user->id)) . '" class="text-primary" target="_blank">' . $row->user->name . '</a>
                        ';
                })
                ->editColumn('user_email', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->email . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('approved_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->approvedBy->name . '</span>
                        ';
                })
                ->editColumn('approved_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('d M, Y  h:i:s A', strtotime($row->approved_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn">View</button>
                    ';
                return $btn;
                })
                ->with([
                    'totalVerificationsCount' => $totalVerificationsCount,
                ])
                ->rawColumns(['user_name', 'user_email', 'created_at', 'approved_by', 'approved_at', 'action'])
                ->make(true);
        }

        return view('backend.verification.approved');
    }

    public function verificationRequestDelete(string $id)
    {
        $verification = Verification::findOrFail($id);

        unlink(base_path("public/uploads/verification_photo/").$verification->id_front_image);
        unlink(base_path("public/uploads/verification_photo/").$verification->id_with_face_image);

        $verification->delete();
    }
}
