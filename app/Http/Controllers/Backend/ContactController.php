<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ContactController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('contact.unread') , only:['contactUnread', 'contactView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('contact.read') , only:['contactRead', 'contactView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('contact.delete') , only:['contactDelete']),
        ];
    }

    public function contactUnread(Request $request)
    {
        if ($request->ajax()) {
            $query = Contact::select('contacts.*')->where('status', 'Unread');

            $query->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalContactsCount = (clone $query)->count();

            $allContact = $query->get();

            return DataTables::of($allContact)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $action = '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>';

                        $deletePermission = auth()->user()->can('contact.delete');
                        if ($deletePermission) {
                            $action .= ' <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>';
                        }

                    return $action;
                })
                ->with([
                    'totalContactsCount' => $totalContactsCount,
                ])
                ->rawColumns(['created_at', 'action'])
                ->make(true);
        }

        return view('backend.contact.unread');
    }

    public function contactRead(Request $request)
    {
        if ($request->ajax()) {
            $query = Contact::select('contacts.*')->where('status', 'Read');

            $query->orderBy('created_at', 'desc');

            // Clone the query for counts
            $totalContactsCount = (clone $query)->count();

            $allContact = $query->get();

            return DataTables::of($allContact)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return '
                        <span class="badge text-info bg-dark">' . date('d M, Y  h:i:s A', strtotime($row->created_at)) . '</span>
                        ';
                })
                ->addColumn('action', function ($row) {
                    $action = '<button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs viewBtn" data-bs-toggle="modal" data-bs-target=".viewModal">View</button>';

                        $deletePermission = auth()->user()->can('contact.delete');
                        if ($deletePermission) {
                            $action .= ' <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>';
                        }

                    return $action;
                })
                ->with([
                    'totalContactsCount' => $totalContactsCount,
                ])
                ->rawColumns(['created_at', 'action'])
                ->make(true);
        }

        return view('backend.contact.read');
    }

    public function contactView(string $id)
    {
        $contact = Contact::where('id', $id)->first();
        $contact->status = 'Read';
        $contact->save();

        return view('backend.contact.show', compact('contact'));
    }

    public function contactDelete(string $id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();
    }
}
