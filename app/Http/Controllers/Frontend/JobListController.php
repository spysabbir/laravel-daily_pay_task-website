<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

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
                    ->editColumn('worker_charge', function ($row) {
                        $workerCharge = '
                            <span class="badge bg-primary">' . $row->worker_charge .' '. get_site_settings('site_currency_symbol') . '</span>
                        ';
                        return $workerCharge;
                    })
                    ->rawColumns(['worker_charge'])
                    ->make(true);
            }
            return view('frontend.job_list.pending');
        }
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
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs resumeBtn">Resume</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('frontend.job_list.paused');
        }
    }

    public function runningJobCanceled($id)
    {
        $jobPost = JobPost::findOrFail($id);

        $jobPost->status = 'Canceled';

        $jobPost->save();

        return response()->json(['success' => 'Status updated successfully.']);
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
                    ->editColumn('action', function ($row) {
                        $btn = '
                            <a href="' . route('running_job.show', $row->id) . '" class="btn btn-primary btn-xs">View</a>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs pausedBtn">Paused</button>
                            <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs canceledBtn">Canceled</button>
                        ';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('frontend.job_list.running');
        }
    }

    public function runningJobShow()
    {
        return view('frontend.job_list.running_show');
    }

    public function runningJobPausedResume($id)
    {
        $jobPost = JobPost::findOrFail($id);

        if ($jobPost->status == 'Paused') {
            $jobPost->status = 'Running';
        } else if ($jobPost->status == 'Running') {
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
                    ->editColumn('status', function ($row) {
                        $status = '
                            <span class="badge bg-info">' . $row->status . '</span>
                        ';
                        return $status;
                    })
                    ->rawColumns(['status'])
                    ->make(true);
            }
            return view('frontend.job_list.completed');
        }
    }
}
