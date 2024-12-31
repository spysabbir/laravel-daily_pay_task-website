<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Report;
use App\Models\ReportReply;
use App\Models\User;
use App\Notifications\ReportReplyNotification;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('report.pending') , only:['reportPending']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('report.check') , only:['reportView', 'reportReply']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('report.resolved') , only:['reportResolved']),
        ];
    }

    public function reportPending(Request $request)
    {
        if ($request->ajax()) {
            $reports = Report::where('status', 'Pending');

            if ($request->report_id) {
                $reports->where('id', $request->report_id);
            }
            if ($request->user_id) {
                $reports->where('user_id', $request->user_id);
            }
            if ($request->type) {
                $reports->where('type', $request->type);
            }
            if ($request->post_task_id) {
                $reports->where('post_task_id', $request->post_task_id);
            }
            if ($request->proof_task_id) {
                $reports->where('proof_task_id', $request->proof_task_id);
            }
            if ($request->reported_by) {
                $reports->where('reported_by', $request->reported_by);
            }

            $query = $reports->select('reports.*')->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalReportsCount = (clone $query)->count();

            $reportList = $query->get();

            return DataTables::of($reportList)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    if ($row->type == 'User') {
                        return '<span class="badge bg-success text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Post Task') {
                        return '<span class="badge bg-info text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Proof Task') {
                        return '<span class="badge bg-primary text-white">'.$row->type.'</span>';
                    }
                })
                ->editColumn('post_task_id', function ($row) {
                    $post_task_id = $row->post_task_id ? $row->post_task_id : '-';
                    return '
                        <span class="badge bg-dark text-white">'.$post_task_id.'</span>
                    ';
                })
                ->editColumn('proof_task_id', function ($row) {
                    $proof_task_id = $row->proof_task_id ? $row->proof_task_id : '-';
                    return '
                        <span class="badge bg-dark text-white">'.$proof_task_id.'</span>
                    ';
                })
                ->editColumn('reported_user_id', function ($row) {
                    return '
                        <span class="badge bg-dark text-white">'.$row->reported->id.'</span>
                    ';
                })
                ->editColumn('reported_user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->reported->id)) . '" class="text-primary" target="_blank">' . $row->reported->name . '</a>
                    ';
                })
                ->editColumn('report_by_user_id', function ($row) {
                    return '
                        <span class="badge bg-dark text-white">'.$row->reportedBy->id.'</span>
                    ';
                })
                ->editColumn('report_by_user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->reportedBy->id)) . '" class="text-primary" target="_blank">' . $row->reportedBy->name . '</a>
                    ';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    $viewPermission = auth()->user()->can('report.check');
                    $viewBtn = $viewPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>'
                        : '';
                    return $viewBtn;
                })
                ->with([
                    'totalReportsCount' => $totalReportsCount,
                ])
                ->rawColumns(['type', 'post_task_id', 'proof_task_id', 'reported_user_id', 'reported_user_name', 'report_by_user_id', 'report_by_user_name', 'status', 'action'])
                ->make(true);
        }
        return view('backend.report.pending');
    }

    public function reportView(string $id)
    {
        $report = Report::where('id', $id)->first();
        $report_reply = ReportReply::where('report_id', $id)->first();
        return view('backend.report.view', compact('report', 'report_reply'));
    }

    public function reportReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'reply' => 'required',
            'reply_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $report = Report::findOrFail($request->report_id);
            $report->update([
                'status' => $request->status,
            ]);

            $photo_name = null;
            if ($request->file('reply_photo')) {
                $manager = new ImageManager(new Driver());
                $photo_name = $report->user_id."-report_reply_photo_".date('YmdHis').".".$request->file('reply_photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('reply_photo'));
                $image->toJpeg(80)->save(base_path("public/uploads/report_photo/").$photo_name);
            }

            $report_reply = new ReportReply();
            $report_reply->report_id = $request->report_id;
            $report_reply->reply = $request->reply;
            $report_reply->reply_photo = $photo_name;
            $report_reply->replied_by = auth()->user()->id;
            $report_reply->replied_at = now();
            $report_reply->save();

            $user = User::findOrFail($report->reported_by);

            $user->notify(new ReportReplyNotification($report, $report_reply));

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function reportFalse(Request $request)
    {
        if ($request->ajax()) {
            $reports = Report::where('status', 'False');

            if ($request->report_id) {
                $reports->where('id', $request->report_id);
            }
            if ($request->user_id) {
                $reports->where('user_id', $request->user_id);
            }
            if ($request->type) {
                $reports->where('type', $request->type);
            }
            if ($request->post_task_id) {
                $reports->where('post_task_id', $request->post_task_id);
            }
            if ($request->proof_task_id) {
                $reports->where('proof_task_id', $request->proof_task_id);
            }
            if ($request->reported_by) {
                $reports->where('reported_by', $request->reported_by);
            }

            $query = $reports->select('reports.*');

            // Clone the query for counts
            $totalReportsCount = (clone $query)->count();

            $reportList = $query->get();

            return DataTables::of($reportList)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    if ($row->type == 'User') {
                        return '<span class="badge bg-success text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Post Task') {
                        return '<span class="badge bg-info text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Proof Task') {
                        return '<span class="badge bg-primary text-white">'.$row->type.'</span>';
                    }
                })
                ->editColumn('post_task_id', function ($row) {
                    $post_task_id = $row->post_task_id ? $row->post_task_id : '-';
                    return '
                        <span class="badge bg-dark text-white">'.$post_task_id.'</span>
                    ';
                })
                ->editColumn('proof_task_id', function ($row) {
                    $proof_task_id = $row->proof_task_id ? $row->proof_task_id : '-';
                    return '
                        <span class="badge bg-dark text-white">'.$proof_task_id.'</span>
                    ';
                })
                ->editColumn('reported_user_id', function ($row) {
                    return '
                        <span class="badge bg-dark text-white">'.$row->reported->id.'</span>
                    ';
                })
                ->editColumn('reported_user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->reported->id)) . '" class="text-primary" target="_blank">' . $row->reported->name . '</a>
                    ';
                })
                ->editColumn('report_by_user_id', function ($row) {
                    return '
                        <span class="badge bg-dark text-white">'.$row->reportedBy->id.'</span>
                    ';
                })
                ->editColumn('report_by_user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->reportedBy->id)) . '" class="text-primary" target="_blank">' . $row->reportedBy->name . '</a>
                    ';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    $viewPermission = auth()->user()->can('report.check');

                    $viewBtn = $viewPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>'
                        : '';

                    return $viewBtn;
                })
                ->with([
                    'totalReportsCount' => $totalReportsCount,
                ])
                ->rawColumns(['type', 'post_task_id', 'proof_task_id', 'reported_user_id', 'reported_user_name', 'report_by_user_id', 'report_by_user_name', 'status', 'action'])
                ->make(true);
        }
        return view('backend.report.false');
    }

    public function reportReceived(Request $request)
    {
        if ($request->ajax()) {
            $reports = Report::where('status', 'Received');

            if ($request->report_id) {
                $reports->where('id', $request->report_id);
            }
            if ($request->user_id) {
                $reports->where('user_id', $request->user_id);
            }
            if ($request->type) {
                $reports->where('type', $request->type);
            }
            if ($request->post_task_id) {
                $reports->where('post_task_id', $request->post_task_id);
            }
            if ($request->proof_task_id) {
                $reports->where('proof_task_id', $request->proof_task_id);
            }
            if ($request->reported_by) {
                $reports->where('reported_by', $request->reported_by);
            }

            $query = $reports->select('reports.*');

            // Clone the query for counts
            $totalReportsCount = (clone $query)->count();

            $reportList = $query->get();

            return DataTables::of($reportList)
                ->addIndexColumn()
                ->editColumn('type', function ($row) {
                    if ($row->type == 'User') {
                        return '<span class="badge bg-success text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Post Task') {
                        return '<span class="badge bg-info text-white">'.$row->type.'</span>';
                    } else if ($row->type == 'Proof Task') {
                        return '<span class="badge bg-primary text-white">'.$row->type.'</span>';
                    }
                })
                ->editColumn('post_task_id', function ($row) {
                    $post_task_id = $row->post_task_id ? $row->post_task_id : '-';
                    return '
                        <span class="badge bg-dark text-white">'.$post_task_id.'</span>
                    ';
                })
                ->editColumn('proof_task_id', function ($row) {
                    $proof_task_id = $row->proof_task_id ? $row->proof_task_id : '-';
                    return '
                        <span class="badge bg-dark text-white">'.$proof_task_id.'</span>
                    ';
                })
                ->editColumn('reported_user_id', function ($row) {
                    return '
                        <span class="badge bg-dark text-white">'.$row->reported->id.'</span>
                    ';
                })
                ->editColumn('reported_user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->reported->id)) . '" class="text-primary" target="_blank">' . $row->reported->name . '</a>
                    ';
                })
                ->editColumn('report_by_user_id', function ($row) {
                    return '
                        <span class="badge bg-dark text-white">'.$row->reportedBy->id.'</span>
                    ';
                })
                ->editColumn('report_by_user_name', function ($row) {
                    return '
                        <a href="' . route('backend.user.show', encrypt($row->reportedBy->id)) . '" class="text-primary" target="_blank">' . $row->reportedBy->name . '</a>
                    ';
                })
                ->editColumn('created_at', function ($row) {
                    return date('d M Y h:i A', strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) {
                    $viewPermission = auth()->user()->can('report.check');

                    $viewBtn = $viewPermission
                        ? '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>'
                        : '';

                    return $viewBtn;
                })
                ->with([
                    'totalReportsCount' => $totalReportsCount,
                ])
                ->rawColumns(['type', 'post_task_id', 'proof_task_id', 'reported_user_id', 'reported_user_name', 'report_by_user_id', 'report_by_user_name', 'status', 'action'])
                ->make(true);
        }
        return view('backend.report.received');
    }
}
