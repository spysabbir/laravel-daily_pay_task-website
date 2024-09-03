<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\JobPost;
use App\Models\JobProof;
use App\Models\User;
use App\Models\Verification;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;


class UserController extends Controller
{
    public function dashboard(Request $request)
    {
        // $user = $request->user();
        // return $myIp = $request->ip();
        // return $position = Location::get('103.4.119.20');
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

        $manager = new ImageManager(new Driver());
        // id_front_image
        $id_front_image_name = $request->user()->id."-id_front_image".".". $request->file('id_front_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_front_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_front_image_name);
        // id_with_face_image
        $id_with_face_image_name = $request->user()->id."-id_with_face_image".".". $request->file('id_with_face_image')->getClientOriginalExtension();
        $image = $manager->read($request->file('id_with_face_image'));
        $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_with_face_image_name);

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

    public function reVerificationStore(Request $request)
    {
        $request->validate([
            'id_type' => 'required|in:NID,Passport,Driving License',
            'id_number' => 'required|string|max:255|unique:verifications,id_number,'.$request->user()->id.',user_id',
            'id_front_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_with_face_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $verification = Verification::where('id', $request->verification_id)->first();

        $manager = new ImageManager(new Driver());
        // id_front_image
        if ($request->file('id_front_image')) {
            unlink(base_path("public/uploads/verification_photo/").$verification->id_front_image);
            $id_front_image_name = $request->user()->id."-id_front_image".".". $request->file('id_front_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('id_front_image'));
            $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_front_image_name);
            $verification->update([
                'id_front_image' => $id_front_image_name,
            ]);
        }
        // id_with_face_image
        if ($request->file('id_with_face_image')) {
            unlink(base_path("public/uploads/verification_photo/").$verification->id_with_face_image);
            $id_with_face_image_name = $request->user()->id."-id_with_face_image".".". $request->file('id_with_face_image')->getClientOriginalExtension();
            $image = $manager->read($request->file('id_with_face_image'));
            $image->toJpeg(80)->save(base_path("public/uploads/verification_photo/").$id_with_face_image_name);
            $verification->update([
                'id_with_face_image' => $id_with_face_image_name,
            ]);
        }

        $verification->update([
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Id Verification request updated successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function deposit(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = Deposit::where('user_id', Auth::id());

                if ($request->status) {
                    $query->where('deposits.status', $request->status);
                }

                $query->select('deposits.*')->orderBy('created_at', 'desc');

                $deposits = $query->get();

                return DataTables::of($deposits)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
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
                    ->rawColumns(['created_at', 'status'])
                    ->make(true);
            }

            $total_deposit = Deposit::where('user_id', $request->user()->id)->where('status', 'Approved')->sum('amount');

            return view('frontend.deposit.index', compact('total_deposit'));
        }
    }

    public function depositStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string|max:255',
            'number' => 'required|string|min:11|max:14',
            'transaction_id' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            if ($request->amount < get_default_settings('min_deposit_amount') || $request->amount > get_default_settings('max_deposit_amount')) {
                return response()->json([
                    'status' => 401,
                    'error'=> 'The amount must be between '.get_site_settings('site_currency_symbol') .get_default_settings('min_deposit_amount').' and '.get_site_settings('site_currency_symbol') .get_default_settings('max_deposit_amount') .' to deposit'
                ]);
            }else {
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
    }

    public function withdraw(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = Withdraw::where('user_id', Auth::id());

                if ($request->status) {
                    $query->where('withdraws.status', $request->status);
                }

                $query->select('withdraws.*')->orderBy('created_at', 'desc');

                $withdraws = $query->get();

                return DataTables::of($withdraws)
                    ->addIndexColumn()
                    ->editColumn('type', function ($row) {
                        if ($row->type == 'Ragular') {
                            $type = '
                            <span class="badge bg-dark">' . $row->type . '</span>
                            ';
                        } else {
                            $type = '
                            <span class="badge bg-primary">' . $row->type . '</span>
                            ';
                        }
                        return $type;
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
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
                    ->rawColumns(['type', 'created_at', 'status'])
                    ->make(true);
            }

            $total_withdraw = Withdraw::where('user_id', $request->user()->id)->where('status', 'Approved')->sum('amount');

            return view('frontend.withdraw.index', compact('total_withdraw'));
        }
    }

    public function withdrawStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Ragular,Instant',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string',
            'number' => 'required|string|min:11|max:14',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            if ($request->amount < get_default_settings('min_withdraw_amount') || $request->amount > get_default_settings('max_withdraw_amount')) {
                return response()->json([
                    'status' => 401,
                    'error'=> 'The amount must be between '.get_site_settings('site_currency_symbol') .get_default_settings('min_withdraw_amount').' and '.get_site_settings('site_currency_symbol') .get_default_settings('max_withdraw_amount').' to withdraw'
                ]);
            }else {
                if ($request->amount > $request->user()->withdraw_balance) {
                    return response()->json([
                        'status' => 402,
                        'error'=> 'Insufficient balance in your account to withdraw '.get_site_settings('site_currency_symbol') . $request->amount .' . Your current balance is '.get_site_settings('site_currency_symbol') . $request->user()->withdraw_balance
                    ]);
                }else {
                    if ($request->type == 'Instant') {
                        $payable_amount = $request->amount - ($request->amount * get_default_settings('withdraw_charge_percentage') / 100) - get_default_settings('instant_withdraw_charge');
                    } else {
                        $payable_amount = $request->amount - ($request->amount * get_default_settings('withdraw_charge_percentage') / 100);
                    }
                    Withdraw::create([
                        'type' => $request->type,
                        'user_id' => $request->user()->id,
                        'amount' => $request->amount,
                        'method' => $request->method,
                        'number' => $request->number,
                        'payable_amount' => $payable_amount,
                        'status' => 'Pending',
                    ]);

                    User::where('id', $request->user()->id)->update([
                        'withdraw_balance' => $request->user()->withdraw_balance - $request->amount,
                    ]);

                    return response()->json([
                        'status' => 200,
                    ]);
                }
            }
        }
    }

    public function findWorks(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $jobProofs = JobProof::where('user_id', Auth::id())->pluck('job_post_id')->toArray();
                $query = JobPost::where('status', 'Running')->whereNotIn('id', $jobProofs)->whereNot('user_id', Auth::id());

                if ($request->category_id) {
                    $query->where('job_posts.category_id', $request->category_id)->orderBy('created_at', 'desc');
                }
                if ($request->sort_by) {
                    if ($request->sort_by == 'low_to_high') {
                        $query->orderBy('worker_charge', 'asc');
                    } else if ($request->sort_by == 'high_to_low') {
                        $query->orderBy('worker_charge', 'desc');
                    } else if ($request->sort_by == 'latest') {
                        $query->orderBy('created_at', 'desc');
                    } else if ($request->sort_by == 'oldest') {
                        $query->orderBy('created_at', 'asc');
                    }
                }

                $query->select('job_posts.*');

                $jobPosts = $query->get();

                return DataTables::of($jobPosts)
                    ->addIndexColumn()
                    ->editColumn('category_name', function ($row) {
                        return $row->category->name;
                    })
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('work.details', encrypt($row->id)).'" title="'.$row->title.'" class="text-info">
                                '.$row->title.'
                            </a>
                        ';
                    })
                    ->editColumn('need_worker', function ($row) {
                        $proofCount = JobProof::where('job_post_id', $row->id)->where('status', '!=', 'Rejected')->count();
                        return $proofCount.' / '.$row->need_worker;
                    })
                    ->editColumn('worker_charge', function ($row) {
                        return $row->worker_charge . ' ' . get_site_settings('site_currency_symbol');
                    })
                    ->editColumn('action', function ($row) {
                        $action = '
                        <a href="'.route('work.details', encrypt($row->id)).'" target="_blank" title="View" class="btn btn-info btn-sm">
                            View
                        </a>
                        ';
                        return $action;
                    })
                    ->rawColumns(['title', 'action'])
                    ->make(true);
            }
            $categories = Category::where('status', 'Active')->get();
            return view('frontend.find_works.index', compact('categories'));
        }
    }

    public function workDetails($id)
    {
        $id = decrypt($id);
        $workDetails = JobPost::findOrFail($id);
        $workProofExists = JobProof::where('job_post_id', $id)->where('user_id', Auth::id())->exists();
        $proofCount = JobProof::where('job_post_id', $id)->where('status', '!=', 'Rejected')->count();
        return view('frontend.find_works.view', compact('workDetails', 'workProofExists', 'proofCount'));
    }

    public function workProofSubmit(Request $request, $id)
    {
        $id = decrypt($id);
        $workDetails = JobPost::findOrFail($id);

        $rules = [
            'proof_answer' => 'required|string|max:5000',
            'proof_photos' => 'required|array|min:' . $workDetails->extra_screenshots + 1, // Ensure all required photos are uploaded
            'proof_photos.*' => 'required|image|mimes:jpg,jpeg,png|max:2048', // Each photo must be an image
        ];

        $messages = [
            'proof_answer.required' => 'The proof answer is required.',
            'proof_answer.string' => 'The proof answer must be a string.',
            'proof_answer.max' => 'The proof answer may not be greater than 5000 characters.',
            'proof_photos.required' => 'You must upload all required proof photos.',
            'proof_photos.array' => 'The proof photos must be an array.',
            'proof_photos.min' => 'You must upload at least ' . $workDetails->extra_screenshots + 1 . ' proof photos.',
            'proof_photos.*.required' => 'Each proof photo is required.',
            'proof_photos.*.image' => 'Each proof photo must be an image.',
            'proof_photos.*.mimes' => 'Each proof photo must be a file of type: jpg, jpeg, png.',
            'proof_photos.*.max' => 'Each proof photo may not be greater than 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $proofCount = JobProof::where('job_post_id', $id)->where('status', '!=', 'Rejected')->count();

        if ($proofCount >= $workDetails->need_worker) {
            $notification = array(
                'message' => 'Sorry, the required number of workers have already submitted proof for this job.',
                'alert-type' => 'error'
            );

            return back()->with($notification)->withInput();
        }

        $proof_photos = [];
        $manager = new ImageManager(new Driver());
        foreach ($request->file('proof_photos') as $key => $photo) {
            $proof_photo_name = $id . "-" . $request->user()->id . "-proof_photo-".($key+1).".". $photo->getClientOriginalExtension();
            $image = $manager->read($photo);
            $image->toJpeg(80)->save(base_path("public/uploads/job_proof_photo/").$proof_photo_name);
            $proof_photos[] = $proof_photo_name;
        }

        JobProof::create([
            'job_post_id' => $id,
            'user_id' => $request->user()->id,
            'proof_answer' => $request->proof_answer,
            'proof_photos' => json_encode($proof_photos),
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Work proof submitted successfully.',
            'alert-type' => 'success'
        );

        return back()->with($notification);
    }

    public function workApplyStore()
    {
        return response()->json([
            'status' => 200,
        ]);
    }

    public function workListPending(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $jobProofs = JobProof::where('user_id', Auth::id())->where('status', 'Pending');

                $query = $jobProofs->select('job_proofs.*');

                if ($request->filter_date){
                    $query->whereDate('job_proofs.created_at', $request->filter_date);
                }

                $query->whereDate('job_proofs.created_at', '>', now()->subDays(7));

                $workList = $query->get();

                return DataTables::of($workList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('work.details', encrypt($row->job_post_id)).'" title="'.$row->jobPost->title.'" class="text-info">
                                '.$row->jobPost->title.'
                            </a>
                        ';
                    })
                    ->editColumn('worker_charge', function ($row) {
                        return $row->jobPost->worker_charge . ' ' . get_site_settings('site_currency_symbol');
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->rawColumns(['title'])
                    ->make(true);
            }
            return view('frontend.work_list.pending');
        }
    }

    public function workListApproved(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $jobProofs = JobProof::where('user_id', Auth::id())->where('status', 'Approved');
                $query = $jobProofs->select('job_proofs.*');

                if ($request->filter_date){
                    $query->whereDate('job_proofs.approved_at', $request->filter_date);
                }

                $query->whereDate('job_proofs.approved_at', '>', now()->subDays(7));

                $workList = $query->get();

                return DataTables::of($workList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('work.details', encrypt($row->job_post_id)).'" title="'.$row->jobPost->title.'" class="text-info">
                                '.$row->jobPost->title.'
                            </a>
                        ';
                    })
                    ->editColumn('worker_charge', function ($row) {
                        return $row->jobPost->worker_charge . ' ' . get_site_settings('site_currency_symbol') . $row->job_post_id;
                    })
                    ->editColumn('rating', function ($row) {
                        return $row->rating ? $row->rating->rating . ' <i class="fa-solid fa-star text-warning"></i>' : 'Not Rated';
                    })
                    ->editColumn('approved_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->approved_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $action;
                    })
                    ->rawColumns(['title', 'rating', 'action'])
                    ->make(true);
            }
            return view('frontend.work_list.approved');
        }
    }

    public function approvedJobView($id)
    {
        $jobProof = JobProof::findOrFail($id);
        $jobPost = JobPost::findOrFail($jobProof->job_post_id);
        return view('frontend.work_list.approved_view', compact('jobProof', 'jobPost'));
    }

    public function workListRejected(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $jobProofs = JobProof::where('user_id', Auth::id())->where('status', 'Rejected');
                $query = $jobProofs->select('job_proofs.*');

                if ($request->filter_date){
                    $query->whereDate('job_proofs.rejected_at', $request->filter_date);
                }

                $query->whereDate('job_proofs.rejected_at', '>', now()->subDays(7));

                $workList = $query->get();

                return DataTables::of($workList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('work.details', encrypt($row->job_post_id)).'" title="'.$row->jobPost->title.'" class="text-info">
                                '.$row->jobPost->title.'
                            </a>
                        ';
                    })
                    ->editColumn('rejected_reason', function ($row) {
                        return $row->rejected_reason;
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                        ';
                        return $action;
                    })
                    ->rawColumns(['title', 'rating', 'action'])
                    ->make(true);
            }
            return view('frontend.work_list.rejected');
        }
    }

    public function rejectedJobCheck($id)
    {
        $jobProof = JobProof::findOrFail($id);
        $jobPost = JobPost::findOrFail($jobProof->job_post_id);
        return view('frontend.work_list.rejected_check', compact('jobProof' , 'jobPost'));
    }

    public function rejectedJobReviewed(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reviewed_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $jobProof = JobProof::findOrFail($id);

            $jobProof->status = 'Reviewed';
            $jobProof->reviewed_reason = $request->reviewed_reason;
            $jobProof->reviewed_at = now();
            $jobProof->save();

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function workListReviewed(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $jobProofs = JobProof::where('user_id', Auth::id())->where('status', 'Reviewed');
                $query = $jobProofs->select('job_proofs.*');

                if ($request->filter_date){
                    $query->whereDate('job_proofs.reviewed_at', $request->filter_date);
                }

                // $query->whereDate('job_proofs.reviewed_at', '>', now()->subDays(7));

                $workList = $query->get();

                return DataTables::of($workList)
                    ->addIndexColumn()
                    ->editColumn('title', function ($row) {
                        return '
                            <a href="'.route('work.details', encrypt($row->job_post_id)).'" title="'.$row->jobPost->title.'" class="text-info">
                                '.$row->jobPost->title.'
                            </a>
                        ';
                    })
                    ->editColumn('rejected_reason', function ($row) {
                        return $row->rejected_reason;
                    })
                    ->editColumn('rejected_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->rejected_at));
                    })
                    ->addColumn('action', function ($row) {
                        $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>
                        ';
                        return $action;
                    })
                    ->rawColumns(['title', 'rating', 'action'])
                    ->make(true);
            }
            return view('frontend.work_list.reviewed');
        }
    }

    public function reviewedJobView($id)
    {
        $jobProof = JobProof::findOrFail($id);
        $jobPost = JobPost::findOrFail($jobProof->job_post_id);
        return view('frontend.work_list.reviewed_check', compact('jobProof' , 'jobPost'));
    }

    public function notification(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $notifications = $user->notifications;

            return DataTables::of($notifications)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    return class_basename($row->type);
                })
                ->editColumn('title', function ($row) {
                    return $row->data['title'];
                })
                ->editColumn('message', function ($row) {
                    return $row->data['message'];
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->diffForHumans();
                })
                ->editColumn('status', function ($row) {
                    if ($row->read_at) {
                        $status = '
                        <span class="badge bg-success">Read</span>
                        ';
                    } else {
                        $status = '
                        <span class="badge bg-danger">Unread</span>
                        ';
                    }
                    return $status;
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('frontend.notification.index');
    }

    public function notificationRead($id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return redirect()->route('notification');
    }

    public function notificationReadAll()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->route('notification');
    }

    public function refferal()
    {
        return view('frontend.refferal.index');
    }
}
