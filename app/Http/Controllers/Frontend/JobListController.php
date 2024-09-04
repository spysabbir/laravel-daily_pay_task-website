<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
use App\Models\JobPost;
use App\Models\JobProof;
use App\Models\Rating;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\UserDetail;
use App\Notifications\BonusNotification;
use App\Notifications\RatingNotification;

class JobListController extends Controller
{
    public function jobListPending(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = JobPost::where('user_id', Auth::id())->where('status', 'Pending');

                $query->select('job_posts.*')->orderBy('created_at', 'desc');

                $jobListPending = $query->get();

                return DataTables::of($jobListPending)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return $row->created_at->format('d M Y h:i A');
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $actionBtn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('frontend.job_list.pending');
        }
    }

    public function jobView($id)
    {
        $jobPost = JobPost::findOrFail($id);
        return view('frontend.job_list.view', compact('jobPost'));
    }

    public function jobListRejected(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = JobPost::where('user_id', Auth::id())->where('status', 'Rejected');

                $query->select('job_posts.*')->orderBy('created_at', 'desc');

                $jobListRejected = $query->get();

                return DataTables::of($jobListRejected)
                    ->addIndexColumn()
                    ->editColumn('worker_charge', function ($row) {
                        $workerCharge = '
                            <span class="badge bg-primary">' . $row->worker_charge .' '. get_site_settings('site_currency_symbol') . '</span>
                        ';
                        return $workerCharge;
                    })
                    ->addColumn('action', function ($row) {
                        $actionBtn = '
                            <a href="' . route('post_job.edit', $row->id) . '" class="btn btn-primary btn-sm">Edit</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $actionBtn;
                    })
                    ->rawColumns(['worker_charge', 'action'])
                    ->make(true);
            }
            return view('frontend.job_list.rejected');
        }
    }

    public function jobListCanceled(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = JobPost::where('user_id', Auth::id())->where('status', 'Canceled');

                $query->select('job_posts.*')->orderBy('created_at', 'desc');

                $jobListCanceled = $query->get();

                return DataTables::of($jobListCanceled)
                    ->addIndexColumn()
                    ->editColumn('proof_submitted', function ($row) {
                        $proofSubmitted = JobProof::where('job_post_id', $row->id)->count();
                        return  '<span class="badge bg-dark">' . $proofSubmitted . ' / ' . $row->need_worker . '</span>';
                    })
                    ->editColumn('proof_check', function ($row) {
                        $proofSubmitted = JobProof::where('job_post_id', $row->id)->count();
                        $proofCheck = JobProof::where('job_post_id', $row->id)->where('status', '!=', 'Pending')->count();
                        return  '<span class="badge bg-dark">' . $proofCheck . ' / ' . $proofSubmitted . '</span>';
                    })
                    ->editColumn('canceled_at', function ($row) {
                        return $row->canceled_by == auth()->user()->id ? date('d M Y h:i A', strtotime($row->canceled_at)) : 'Canceled by ' . $row->canceledBy->name . ' at ' . date('d M Y h:i A', strtotime($row->canceled_at));
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_check', 'action'])
                    ->make(true);
            }
            return view('frontend.job_list.canceled');
        }
    }

    public function jobListPaused(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = JobPost::where('user_id', Auth::id())->where('status', 'Paused');

                $query->select('job_posts.*')->orderBy('created_at', 'desc');

                $jobListPaused = $query->get();

                return DataTables::of($jobListPaused)
                    ->addIndexColumn()
                    ->editColumn('proof_submitted', function ($row) {
                        $proofSubmitted = JobProof::where('job_post_id', $row->id)->count();
                        return  '<span class="badge bg-dark">' . $proofSubmitted . ' / ' . $row->need_worker . '</span>';
                    })
                    ->editColumn('proof_check', function ($row) {
                        $proofSubmitted = JobProof::where('job_post_id', $row->id)->count();
                        $proofCheck = JobProof::where('job_post_id', $row->id)->where('status', '!=', 'Pending')->count();
                        return  '<span class="badge bg-dark">' . $proofCheck . ' / ' . $proofSubmitted . '</span>';
                    })
                    ->editColumn('paused_at', function ($row) {
                        return $row->paused_by == auth()->user()->id ? date('d M Y h:i A', strtotime($row->paused_at)) : 'Paused by ' . $row->pausedBy->name . ' at ' . date('d M Y h:i A', strtotime($row->paused_at));
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <a href="' . route('running_job.show', encrypt($row->id)) . '" class="btn btn-success btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs resumeBtn">Resume</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_check', 'action'])
                    ->make(true);
            }
            return view('frontend.job_list.paused');
        }
    }

    public function runningJobCanceled(Request $request, $id)
    {
        $jobPost = JobPost::findOrFail($id);

        $jobProofs = JobProof::where('job_post_id', $id)->where('status', 'Pending')->count();

        if ($jobProofs > 0) {
            return response()->json([
                'status' => 400,
                'error' => 'You can not cancel this job. Because some workers are working on this job.'
            ]);
        }

        if ($request->has('check') && $request->check == true) {
            return response()->json([
                'status' => 200,
                'message' => 'Job found. Proceed with cancellation.'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 401,
                'error' => 'Please enter a valid reason.'
            ]);
        } else {
            $jobPost->status = 'Canceled';
            $jobPost->cancellation_reason = $request->message;
            $jobPost->canceled_by = auth()->user()->id;
            $jobPost->canceled_at = now();

            $jobPost->save();

            return response()->json([
                'status' => 200,
                'success' => 'Status updated successfully.'
            ]);
        }
    }

    public function jobListRunning(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = JobPost::where('user_id', Auth::id())->where('status', 'Running');

                $query->select('job_posts.*')->orderBy('created_at', 'desc');

                $jobListRunning = $query->get();

                return DataTables::of($jobListRunning)
                    ->addIndexColumn()
                    ->editColumn('proof_submitted', function ($row) {
                        $proofSubmitted = JobProof::where('job_post_id', $row->id)->count();
                        return  '<span class="badge bg-dark">' . $proofSubmitted . ' / ' . $row->need_worker . '</span>';
                    })
                    ->editColumn('proof_check', function ($row) {
                        $proofSubmitted = JobProof::where('job_post_id', $row->id)->count();
                        $proofCheck = JobProof::where('job_post_id', $row->id)->where('status', '!=', 'Pending')->count();
                        return  '<span class="badge bg-dark">' . $proofCheck . ' / ' . $proofSubmitted . '</span>';
                    })
                    ->editColumn('approved_at', function ($row) {
                        return  date('d M Y h:i A', strtotime($row->approved_at));
                    })
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
                            <a href="' . route('running_job.show', encrypt($row->id)) . '" class="btn btn-info btn-xs">Check</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs pausedBtn">Paused</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['proof_submitted', 'proof_check', 'approved_at', 'action'])
                    ->make(true);
            }
            return view('frontend.job_list.running');
        }
    }

    public function runningJobEdit(string $id)
    {
        $jobPost = JobPost::where('id', $id)->first();
        return response()->json($jobPost);
    }

    public function runningJobUpdate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'need_worker' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $jobPost = JobPost::findOrFail($id);

                $job_post_charge = (($request->need_worker * $jobPost->worker_charge));

                $site_charge = $job_post_charge * get_default_settings('job_posting_charge_percentage') / 100;

                $total_charge = $job_post_charge + $site_charge;

            if ($request->user()->deposit_balance < $total_charge) {
                return response()->json([
                    'status' => 401,
                    'error' => 'Insufficient balance.'
                ]);
            } else {


                $request->user()->update([
                    'deposit_balance' => $request->user()->deposit_balance - $total_charge,
                ]);

                JobPost::where('id', $id)->update([
                    'need_worker' => $jobPost->need_worker + $request->need_worker,
                    'charge' => $jobPost->charge + $job_post_charge,
                    'site_charge' => $jobPost->site_charge + $site_charge,
                    'total_charge' => $jobPost->total_charge + $total_charge,
                ]);

                return response()->json([
                    'status' => 200,
                ]);
            }
        }
    }

    public function runningJobShow(Request $request, $id)
    {
        if ($request->ajax()) {
            $query = JobProof::where('job_post_id', decrypt($id));

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $query->select('job_proofs.*');

            $JobProofs = $query->get();

            return DataTables::of($JobProofs)
                ->addColumn('checkbox', function ($row) {
                    $checkPending = $row->status != 'Pending' ? 'disabled' : '';
                    $checkbox = '
                        <input type="checkbox" class="form-check-input checkbox" value="' . $row->id . '" ' . $checkPending . '>
                    ';
                    return $checkbox;
                })
                ->editColumn('user', function ($row) {
                    $userDetail = UserDetail::where('user_id', $row->user_id)->first();
                    $user = '
                        <span class="badge bg-dark">Name: ' . $row->user->name . '</span>
                        <span class="badge bg-dark">Ip: ' . $userDetail->ip . '</span>
                    ';
                    return $user;
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Pending') {
                        $status = '<span class="badge bg-warning">' . $row->status . '</span>';
                    } else if ($row->status == 'Approved') {
                        $status = '<span class="badge bg-success">' . $row->status . '</span>';
                    } else if ($row->status == 'Rejected') {
                        $status = '<span class="badge bg-danger">' . $row->status . '</span>';
                    }else {
                        $status = '<span class="badge bg-info">' . $row->status . '</span>';
                    }
                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d M Y h:i A');
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">Check</button>';
                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'user', 'status', 'created_at', 'action'])
                ->make(true);
        }


        $jobPost = JobPost::findOrFail(decrypt($id));
        return view('frontend.job_list.running_show', compact('jobPost'));
    }

    public function runningJobApprovedAll($id)
    {
        $jobProofs = JobProof::where('job_post_id', $id)->where('status', 'Pending')->get();

        $jobPost = JobPost::findOrFail($id);

        foreach ($jobProofs->pluck('user_id') as $user_id) {
            $user = User::findOrFail($user_id);
            $user->withdraw_balance = $user->withdraw_balance + $jobPost->worker_charge;
            $user->save();
        }

        foreach ($jobProofs as $jobProof) {
            $jobProof->status = 'Approved';
            $jobProof->approved_at = now();
            $jobProof->approved_by = auth()->user()->id;
            $jobProof->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function runningJobSelectedItemApproved(Request $request)
    {
        $jobProofs = JobProof::whereIn('id', $request->id)->get();

        $jobPost = JobPost::findOrFail($jobProofs->first()->job_post_id);

        foreach ($jobProofs as $jobProof) {
            $user = User::findOrFail($jobProof->user_id);
            $user->withdraw_balance = $user->withdraw_balance + $jobPost->worker_charge;
            $user->save();
        }

        foreach ($jobProofs as $jobProof) {
            $jobProof->status = 'Approved';
            $jobProof->approved_at = now();
            $jobProof->approved_by = auth()->user()->id;
            $jobProof->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function runningJobSelectedItemRejected(Request $request)
    {
        $jobProofs = JobProof::whereIn('id', $request->id)->get();

        foreach ($jobProofs as $jobProof) {
            $jobProof->status = 'Rejected';
            $jobProof->rejected_reason = $request->message;
            $jobProof->rejected_at = now();
            $jobProof->rejected_by = auth()->user()->id;
            $jobProof->save();
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function runningJobProofCheck($id)
    {
        $jobProof = JobProof::findOrFail($id);
        return view('frontend.job_list.proof_check', compact('jobProof'));
    }

    public function runningJobProofCheckUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'bonus' => 'required|numeric|min:0|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            if ($request->status == 'Rejected') {
                $validator = Validator::make($request->all(), [
                    'rejected_reason' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 400,
                        'error' => $validator->errors()->toArray()
                    ]);
                }
            }

            $jobProof = JobProof::findOrFail($id);

            $jobPost = JobPost::findOrFail($jobProof->job_post_id);

            $user = User::findOrFail($jobProof->user_id);
            $user->withdraw_balance = $user->withdraw_balance + $jobPost->worker_charge + $request->bonus;
            $user->save();

            if ($request->rating) {
                Rating::create([
                    'user_id' => $jobProof->user_id,
                    'job_post_id' => $jobPost->id,
                    'rating' => $request->rating,
                ]);

                $rating = Rating::where('user_id', $jobProof->user_id)->where('job_post_id', $jobPost->id)->first();

                $user->notify(new RatingNotification($rating));
            }

            if ($request->bonus) {
                $bonus = $request->bonus;
                $user->notify(new BonusNotification($bonus));
            }

            $jobProof->status = $request->status;
            $jobProof->rejected_reason = $request->rejected_reason;
            $jobProof->rejected_at = $request->status == 'Rejected' ? now() : NULL;
            $jobProof->rejected_by = $request->status == 'Rejected' ? auth()->user()->id : NULL;
            $jobProof->approved_at = $request->status == 'Approved' ? now() : NULL;
            $jobProof->approved_by = $request->status == 'Approved' ? auth()->user()->id : NULL;
            $jobProof->save();

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function runningJobPausedResume($id)
    {
        $jobPost = JobPost::findOrFail($id);

        if ($jobPost->status == 'Paused') {
            $jobPost->status = 'Running';
        } else if ($jobPost->status == 'Running') {
            $jobPost->paused_at = now();
            $jobPost->paused_by = auth()->user()->id;
            $jobPost->status = 'Paused';
        }

        $jobPost->save();

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function jobListCompleted(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            if ($request->ajax()) {
                $query = JobPost::where('user_id', Auth::id())->where('status', 'Completed');

                $query->select('job_posts.*')->orderBy('created_at', 'desc');

                $jobListCompleted = $query->get();

                return DataTables::of($jobListCompleted)
                    ->addIndexColumn()
                    ->editColumn('proof_status', function ($row) {
                        $approvedProof = JobProof::where('job_post_id', $row->id)->where('status', 'Approved')->count();
                        $rejectedProof = JobProof::where('job_post_id', $row->id)->where('status', 'Rejected')->count();
                        $proofStatus = '
                            <span class="badge bg-success"> Approved: ' . $approvedProof . '</span>
                            <span class="badge bg-danger"> Rejected: ' . $rejectedProof . '</span>
                        ';
                        return $proofStatus;
                    })
                    ->editColumn('total_charge', function ($row) {
                        $workerCharge = '
                            <span class="badge bg-primary">' . $row->total_charge .' '. get_site_settings('site_currency_symbol') . '</span>
                        ';
                        return $workerCharge;
                    })
                    ->editColumn('charge_status', function ($row) {
                        $rejectedProof = JobProof::where('job_post_id', $row->id)->where('status', 'Rejected')->count();
                        $proofStatus = '
                            <span class="badge bg-success"> Expencese: ' . $row->total_charge - ($row->worker_charge * $rejectedProof) .' '. get_site_settings('site_currency_symbol') . '</span>
                            <span class="badge bg-danger"> Return: ' . $row->worker_charge * $rejectedProof .' '. get_site_settings('site_currency_symbol') . '</span>
                        ';
                        return $proofStatus;
                    })
                    ->addColumn('action', function ($row) {
                        $status = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>
                        ';
                        return $status;
                    })
                    ->rawColumns(['proof_status', 'total_charge', 'charge_status', 'action'])
                    ->make(true);
            }
            return view('frontend.job_list.completed');
        }
    }
}
