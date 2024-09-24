<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Block;
use App\Models\Bonus;
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
use App\Models\Report;
use App\Models\ReportReply;
use App\Models\Support;
use App\Events\SupportEvent;
use App\Events\MessageEvent;


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
            'id_number' => 'required|string|min:10|unique:verifications,id_number,'.$request->user()->id.',user_id',
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
        $minWithdrawAmount = get_default_settings('min_withdraw_amount');
        $maxWithdrawAmount = get_default_settings('max_withdraw_amount');
        $currencySymbol = get_site_settings('site_currency_symbol');
        $withdrawChargePercentage = get_default_settings('withdraw_charge_percentage');
        $instantWithdrawCharge = get_default_settings('instant_withdraw_charge');

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Ragular,Instant',
            'amount' => "required|numeric|min:$minWithdrawAmount|max:$maxWithdrawAmount",
            'method' => 'required|string',
            'number' => 'required|string|min:11|max:14',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        }

        if ($request->amount > $request->user()->withdraw_balance) {
            return response()->json([
                'status' => 402,
                'error' => 'Insufficient balance in your account to withdraw ' . $currencySymbol . $request->amount .
                        '. Your current balance is ' . $currencySymbol . $request->user()->withdraw_balance
            ]);
        }

        $payableAmount = $request->amount - ($request->amount * $withdrawChargePercentage / 100);
        if ($request->type == 'Instant') {
            $payableAmount -= $instantWithdrawCharge;
        }

        Withdraw::create([
            'type' => $request->type,
            'user_id' => $request->user()->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'number' => $request->number,
            'payable_amount' => $payableAmount,
            'status' => 'Pending',
        ]);

        $request->user()->decrement('withdraw_balance', $request->amount);

        return response()->json([
            'status' => 200,
        ]);
    }

    public function bonus(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = Bonus::where('user_id', Auth::id());

                if ($request->type) {
                    $query->where('bonuses.type', $request->type);
                }

                $query->select('bonuses.*')->orderBy('created_at', 'desc');

                $bonuses = $query->get();

                return DataTables::of($bonuses)
                    ->addIndexColumn()
                    ->editColumn('type', function ($row) {
                        $type = '
                            <span class="badge bg-primary">' . $row->type . '</span>
                        ';
                        return $type;
                    })
                    ->editColumn('bonus_by', function ($row) {
                        return '
                        <a href="'.route('user.profile', encrypt($row->bonusBy->id)).'" title="'.$row->bonusBy->name.'" class="text-info">
                            '.$row->bonusBy->name.'
                        </a>
                    ';
                    })
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->rawColumns(['type', 'bonus_by', 'created_at'])
                    ->make(true);
            }

            $total_bonus = Bonus::where('user_id', Auth::id())->sum('amount');

            return view('frontend.bonus.index', compact('total_bonus'));
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
                $blockedUsers = Block::where('blocked_by', Auth::id())->pluck('user_id')->toArray();
                $query = JobPost::where('status', 'Running')->whereNotIn('id', $jobProofs)->whereNot('user_id', Auth::id())->whereNotIn('user_id', $blockedUsers);

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
                        $proofSubmitted = JobProof::where('job_post_id', $row->id)->count();
                        $proofStyleWidth = $proofSubmitted != 0 ? round(($proofSubmitted / $row->need_worker) * 100, 2) : 100;
                        $progressBarClass = $proofSubmitted == 0 ? 'primary' : 'success';
                        return '
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-' . $progressBarClass . '" role="progressbar" style="width: ' . $proofStyleWidth . '%" aria-valuenow="' . $proofSubmitted . '" aria-valuemin="0" aria-valuemax="' . $row->need_worker . '">' . $proofSubmitted . '/' . $row->need_worker . '</div>
                        </div>
                        ';
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
                    ->rawColumns(['title', 'need_worker', 'action'])
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
        $proofCount = JobProof::where('job_post_id', $id)->count();
        $blocked = Block::where('user_id', $workDetails->user_id)->where('blocked_by', Auth::id())->exists();
        return view('frontend.find_works.view', compact('workDetails', 'workProofExists', 'proofCount', 'blocked'));
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

        $proofCount = JobProof::where('job_post_id', $id)->count();

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
                        return $row->jobPost->worker_charge . ' ' . get_site_settings('site_currency_symbol');
                    })
                    ->editColumn('rating', function ($row) {
                        return $row->rating ? $row->rating->rating . ' <i class="fa-solid fa-star text-warning"></i>' : 'Not Rated';
                    })
                    ->editColumn('bonus', function ($row) {
                        return $row->bonus ? $row->bonus->amount . ' ' . get_site_settings('site_currency_symbol') : 'No Bonus';
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
                    ->rawColumns(['title', 'rating', 'bonus', 'action'])
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
            $reviewedCount = JobProof::where('user_id', Auth::id())->where('reviewed_at', '!=', null)->whereMonth('reviewed_at', now()->month)->count();

            if (30 >= get_default_settings('job_proof_monthly_free_review_time')) {
                if($request->user()->withdraw_balance < get_default_settings('job_proof_additional_review_charge')){
                    return response()->json([
                        'status' => 401,
                        'error' => 'Insufficient balance in your account to review additional job proof.'
                    ]);
                }else{
                    User::where('id', Auth::id())->update([
                        'withdraw_balance' => $request->user()->withdraw_balance - get_default_settings('job_proof_additional_review_charge'),
                    ]);
                }
            }

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
                    ->editColumn('reviewed_at', function ($row) {
                        return date('d M Y h:i A', strtotime($row->reviewed_at));
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

    public function userProfile($id)
    {
        $user = User::findOrFail(decrypt($id));
        $blocked = Block::where('user_id', $user->id)->where('blocked_by', Auth::id())->exists();
        return view('frontend.user_profile.index', compact('user', 'blocked'));
    }

    public function blockList(Request $request)
    {
        if ($request->ajax()) {
            $blockedUsers = Block::where('blocked_by', Auth::id());

            $query = $blockedUsers->select('blocks.*');

            $blockedList = $query->get();

            return DataTables::of($blockedList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return '
                        <a href="'.route('user.profile', encrypt($row->blocked->id)).'" title="'.$row->blocked->name.'" class="text-info">
                            '.$row->blocked->name.'
                        </a>
                    ';
                })
                ->editColumn('blocked_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->blocked_at));
                })
                ->addColumn('action', function ($row) {
                    $action = '
                    <a href="'.route('block_user', $row->blocked->id).'" title="Unblock" class="btn btn-danger btn-sm">
                        Unblock
                    </a>
                    ';
                    return $action;
                })
                ->rawColumns(['user', 'action'])
                ->make(true);
        }
        return view('frontend.block_list.index');
    }

    public function blockUser($id)
    {
        $blocked = Block::where('user_id', $id)->where('blocked_by', Auth::id())->exists();

        if ($blocked) {
            Block::where('user_id', $id)->where('blocked_by', Auth::id())->delete();

            $notification = array(
                'message' => 'User unblocked successfully.',
                'alert-type' => 'success'
            );

            return back()->with($notification);
        }

        Block::create([
            'user_id' => $id,
            'blocked_by' => Auth::id(),
            'blocked_at' => now(),
        ]);

        $notification = array(
            'message' => 'User blocked successfully.',
            'alert-type' => 'error'
        );

        return back()->with($notification);
    }

    public function reportList(Request $request)
    {
        if ($request->ajax()) {
            $reportedUsers = Report::where('reported_by', Auth::id());

            $query = $reportedUsers->select('reports.*');

            if ($request->status) {
                $query->where('reports.status', $request->status);
            }

            $reportedList = $query->get();

            return DataTables::of($reportedList)
                ->addIndexColumn()
                ->editColumn('user', function ($row) {
                    return '
                        <a href="'.route('user.profile', encrypt($row->reported->id)).'" title="'.$row->reported->name.'" class="text-info">
                            '.$row->reported->name.'
                    ';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->created_at));
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '
                        <span class="badge bg-warning">' . $row->status . '</span>
                        ';
                    } else {
                        $status = '
                        <span class="badge bg-success">' . $row->status . '</span>
                        ';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $action = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                    ';
                    return $action;
                })
                ->rawColumns(['user', 'status', 'action'])
                ->make(true);
        }
        return view('frontend.report_list.index');
    }

    public function reportView($id)
    {
        $report = Report::findOrFail($id);
        $report_reply = ReportReply::where('report_id', $id)->first();
        return view('frontend.report_list.view', compact('report', 'report_reply'));
    }

    public function reportUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = $id."-report_photo".date('YmdHis').".".$request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/report_photo/").$photo_name);
            }

            Report::create([
                'user_id' => $id,
                'reported_by' => Auth::id(),
                'reason' => $request->reason,
                'photo' => $photo_name,
                'status' => 'Pending',
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function support(){
        $supports = Support::where('sender_id', Auth::id())->orWhere('receiver_id', Auth::id())->get();

        Support::where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
            $message->status = 'Read';
            $message->save();
        });

        return view('frontend.support.index' , compact('supports'));
    }

    public function supportSendMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $photo_name = null;
            if ($request->file('photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = Auth::id()."-support_photo_".date('YmdHis').".".$request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/support_photo/").$photo_name);
            }

            $support = Support::create([
                'sender_id' => Auth::id(),
                'receiver_id' => 1,
                'message' => $request->message,
                'photo' => $photo_name,
            ]);

            Support::where('receiver_id', Auth::id())->where('status', 'Unread')->each(function ($message) {
                $message->status = 'Read';
                $message->save();
            });

            SupportEvent::dispatch($support);

            return response()->json([
                'status' => 200,
                'support' => $support,
            ]);
        }
    }
}
