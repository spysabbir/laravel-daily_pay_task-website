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
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('contact.index') , only:['contact', 'contactView']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('contact.delete') , only:['contactDelete']),
        ];
    }

    public function contact(Request $request)
    {
        if ($request->ajax()) {
            $query = Contact::select('contacts.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $allContact = $query->get();

            return DataTables::of($allContact)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Read') {
                        return '<span class="badge text-white bg-success">Read</span>';
                    } else {
                        return '<span class="badge text-white bg-danger">Unread</span>';
                    }
                })
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
                ->rawColumns(['status', 'created_at', 'action'])
                ->make(true);
        }

        return view('backend.contact.index');
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
