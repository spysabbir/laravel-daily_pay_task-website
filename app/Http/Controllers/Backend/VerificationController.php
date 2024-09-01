<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Verification;
use App\Notifications\VerificationNotification;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class VerificationController extends Controller
{
    public function verificationRequest(Request $request)
    {
        if ($request->ajax()) {
            $query = Verification::where('status', 'Pending');

            $query->select('verifications.*')->orderBy('created_at', 'desc');

            $pendingRequest = $query->get();

            return DataTables::of($pendingRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
                        ';
                })
                ->editColumn('user_email', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->email . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user_name', 'user_email', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.verification.index');
    }

    public function verificationRequestShow(string $id)
    {
        $verification = Verification::where('id', $id)->first();
        return view('backend.verification.show', compact('verification'));
    }

    public function verificationRequestStatusChange(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $verification = Verification::findOrFail($id);
            $verification->update([
                'status' => $request->status,
                'remarks' => $request->remarks,
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
            }

            $user->notify(new VerificationNotification($verification));

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function verificationRequestRejected(Request $request)
    {
        if ($request->ajax()) {
            $query = Verification::where('status', 'Rejected');

            $query->select('verifications.*')->orderBy('rejected_at', 'desc');

            $rejectedData = $query->get();

            return DataTables::of($rejectedData)
                ->addIndexColumn()
                ->editColumn('user_email', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->email . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('rejected_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->rejectedBy->name . '</span>
                        ';
                })
                ->editColumn('rejected_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->rejected_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user_email', 'created_at', 'rejected_by', 'rejected_at', 'action'])
                ->make(true);
        }

        return view('backend.verification.index');
    }

    public function verificationRequestDelete(string $id)
    {
        $verification = Verification::findOrFail($id);

        unlink(base_path("public/uploads/verification_photo/").$verification->id_front_image);
        unlink(base_path("public/uploads/verification_photo/").$verification->id_with_face_image);

        $verification->delete();
    }

    public function verificationRequestApproved(Request $request)
    {
        if ($request->ajax()) {
            $query = Verification::where('status', 'Approved');

            $query->select('verifications.*')->orderBy('created_at', 'desc');

            $approvedRequest = $query->get();

            return DataTables::of($approvedRequest)
                ->addIndexColumn()
                ->editColumn('user_name', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->name . '</span>
                        ';
                })
                ->editColumn('user_email', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->user->email . '</span>
                        ';
                })
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->editColumn('approved_by', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . $row->approvedBy->name . '</span>
                        ';
                })
                ->editColumn('approved_at', function ($row) {
                    return '
                        <span class="badge text-dark bg-light">' . date('F j, Y  H:i:s A', strtotime($row->approved_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                    <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                    ';
                return $btn;
                })
                ->rawColumns(['user_name', 'user_email', 'created_at', 'approved_by', 'approved_at', 'action'])
                ->make(true);
        }

        return view('backend.verification.approved');
    }
}
