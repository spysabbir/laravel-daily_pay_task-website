<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Verification;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function dashboard()
    {
        return view('frontend/dashboard');
    }

    public function profileEdit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }

    public function profileSetting(Request $request)
    {
        $user = $request->user();
        return view('profile.setting', compact('user'));
    }

    public function verification(Request $request)
    {
        $verification = Verification::where('user_id', $request->user()->id)->first();
        $user = $request->user();
        return view('frontend.verification.index', compact('user', 'verification'));
    }

    public function verificationStore(Request $request)
    {
        $request->validate([
            'id_type' => 'required|in:NID,Passport,Driving License',
            'id_number' => 'required|string|max:255|unique:verifications,id_number,'.$request->user()->id.',user_id',
            'id_front_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'id_with_face_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $verification = Verification::where('user_id', $request->user()->id)->first();

        if ($verification) {
            unlink(base_path("public/uploads/verification_photo/").$verification->id_front_image);
            unlink(base_path("public/uploads/verification_photo/").$verification->id_with_face_image);
        }

        $manager = new ImageManager(new Driver());
        // id_front_image
        $id_front_image_name = $request->user()->id."-id_front_image".".". $request->file('id_front_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_front_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_front_image_name);
        // id_with_face_image
        $id_with_face_image_name = $request->user()->id."-id_with_face_image".".". $request->file('id_with_face_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_with_face_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_with_face_image_name);

        if ($verification) {
            $verification->update([
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'id_front_image' => $id_front_image_name,
                'id_with_face_image' => $id_with_face_image_name,
                'status' => 'Pending',
            ]);

            $notification = array(
                'message' => 'Id Verification request updated successfully.',
                'alert-type' => 'success'
            );

            return back()->with($notification);
        }

        Verification::create([
            'user_id' => $request->user()->id,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'id_front_image' => $id_front_image_name,
            'id_with_face_image' => $id_with_face_image_name,
        ]);

        $notification = array(
            'message' => 'Id Verification request submitted successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function deposit(Request $request)
    {
        if ($request->ajax()) {
            $query = Deposit::select('deposits.*');

            if ($request->status) {
                $query->where('deposits.status', $request->status);
            }

            $query->orderBy('created_at', 'desc');

            $deposits = $query->get();

            return DataTables::of($deposits)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '
                        <span class="badge bg-success">' . $row->status . '</span>
                        ';
                    } else if ($row->status == 'Approved') {
                        $status = '
                        <span class="badge text-white bg-info">' . $row->status . '</span>
                        ';
                    } else {
                        $status = '
                        <span class="badge bg-danger">' . $row->status . '</span>
                        ';
                    }
                    return $status;
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('frontend.deposit.index');
    }

    public function depositStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string|max:255',
            'number' => 'required|string|max:255',
            'transaction_id' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Deposit::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'method' => $request->method,
                'number' => $request->number,
                'transaction_id' => $request->transaction_id,
                'status' => 'Pending',
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function withdraw(Request $request)
    {
        if ($request->ajax()) {
            $query = Withdraw::select('withdraws.*');

            if ($request->status) {
                $query->where('withdraws.status', $request->status);
            }

            $query->orderBy('created_at', 'desc');

            $withdraws = $query->get();

            return DataTables::of($withdraws)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '
                        <span class="badge bg-success">' . $row->status . '</span>
                        ';
                    } else if ($row->status == 'Approved') {
                        $status = '
                        <span class="badge text-white bg-info">' . $row->status . '</span>
                        ';
                    } else {
                        $status = '
                        <span class="badge bg-danger">' . $row->status . '</span>
                        ';
                    }
                    return $status;
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('frontend.withdraw.index');
    }

    public function withdrawStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string|max:255',
            'number' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Withdraw::create([
                'user_id' => $request->user()->id,
                'amount' => $request->amount,
                'method' => $request->method,
                'number' => $request->number,
                'status' => 'Pending',
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function findWorks()
    {
        return view('frontend.find_works.index');
    }

    public function workDetails()
    {
        return view('frontend.find_works.view');
    }

    public function workApplyStore()
    {
        return response()->json([
            'status' => 200,
        ]);
    }

    public function workListPending()
    {
        return view('frontend.work_list.pending');
    }

    public function workListApproved()
    {
        return view('frontend.work_list.approved');
    }

    public function workListRejected()
    {
        return view('frontend.work_list.rejected');
    }

    public function postJob()
    {
        return view('frontend.post_job.create');
    }

    public function postJobStore()
    {
        return response()->json([
            'status' => 200,
        ]);
    }

    public function jobListRunning()
    {
        return view('frontend.job_list.running');
    }

    public function jobListCompleted()
    {
        return view('frontend.job_list.completed');
    }
}
