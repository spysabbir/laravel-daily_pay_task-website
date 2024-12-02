<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Models\Subscriber;
use App\Models\User;
use App\Mail\NewsletterMail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SubscriberController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('subscriber.index') , only:['subscriber']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('subscriber.delete') , only:['subscriberDelete']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('subscriber.newsletter'), only:['subscriberNewsletter', 'subscriberNewsletterView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('subscriber.newsletter.send') , only:['subscriberNewsletterSend']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('subscriber.newsletter.delete') , only:['subscriberNewsletterDelete']),
        ];
    }

    public function subscriber(Request $request)
    {
        if ($request->ajax()) {
            $query = Subscriber::query();

            // Apply status filter if provided
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Fetch data with sorting
            $subscriberList = $query->orderByDesc('created_at')->get();

            // Return DataTables response
            return DataTables::of($subscriberList)
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => date('d M Y h:i A', strtotime($row->created_at)))
                ->editColumn('status', fn($row) => $row->status == 'Active'
                    ? '<span class="badge bg-success text-white">Active</span>'
                    : '<span class="badge bg-danger text-white">Inactive</span>')
                ->addColumn('action', fn($row) => '<button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>')
                ->rawColumns(['created_at', 'status', 'action'])
                ->make(true);
        }

        return view('backend.subscriber.index');
    }

    public function subscriberDelete(string $id)
    {
        Subscriber::findOrFail($id)->delete();
        return response()->json(['status' => 200, 'message' => 'Subscriber deleted successfully']);
    }

    public function subscriberNewsletter(Request $request)
    {
        if ($request->ajax()) {
            $query = Newsletter::query();

            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->mail_type) {
                $query->where('mail_type', $request->mail_type);
            }

            $newsletterList = $query->orderByDesc('created_at')->get();

            return DataTables::of($newsletterList)
                ->addIndexColumn()
                ->editColumn('mail_type', fn($row) => $row->mail_type == 'Subscriber'
                    ? '<span class="badge bg-info text-white">Subscriber</span>'
                    : '<span class="badge bg-primary text-white">User</span>')
                ->editColumn('created_at', fn($row) => date('d M Y h:i A', strtotime($row->created_at)))
                ->editColumn('status', fn($row) => $row->status == 'Sent'
                    ? '<span class="badge bg-success text-white">Sent</span>'
                    : '<span class="badge bg-warning text-white">Draft</span>')
                ->addColumn('action', function ($row) {
                    $action = '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>';
                    if ($row->status == 'Draft') {
                        $action .= ' <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>';
                    }
                    return $action;
                })
                ->rawColumns(['mail_type', 'created_at', 'status', 'action'])
                ->make(true);
        }

        return view('backend.subscriber.newsletter');
    }

    public function subscriberNewsletterSend(Request $request)
    {
        // Validation rules for both 'Draft' and 'Sent'
        $rules = [
            'mail_type' => 'required|in:Subscriber,User',
            'subject' => 'required|string|max:255',
            'content' => 'required',
            'status' => 'required|in:Draft,Sent',
        ];

        // If status is 'Draft', add 'sent_at' validation
        if ($request->status == 'Draft') {
            $rules['sent_at'] = 'required|date';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 400, 'error' => $validator->errors()->toArray()]);
        }

        $sent_at = $request->status == 'Sent' ? now() : $request->sent_at;

        // Create and save the newsletter
        $newsletter = Newsletter::create([
            'mail_type' => $request->mail_type,
            'subject' => $request->subject,
            'content' => $request->content,
            'status' => $request->status,
            'sent_at' => $sent_at,
            'created_by' => auth()->id(),
        ]);

        // Queue emails if the status is 'Sent'
        if ($request->status == 'Sent') {
            $recipients = $request->mail_type == 'Subscriber'
                ? Subscriber::where('status', 'Active')->pluck('email')
                : User::where('status', 'Active')->pluck('email');

            foreach ($recipients as $email) {
                Mail::to($email)->queue(new NewsletterMail($newsletter));
            }
        }

        return response()->json(['status' => 200, 'message' => 'Newsletter processed successfully']);
    }

    public function subscriberNewsletterView(string $id)
    {
        $newsletter = Newsletter::findOrFail($id);
        return view('backend.subscriber.newsletter_view', compact('newsletter'));
    }

    public function subscriberNewsletterDelete(string $id)
    {
        Newsletter::findOrFail($id)->delete();
        return response()->json(['status' => 200, 'message' => 'Newsletter deleted successfully']);
    }
}
