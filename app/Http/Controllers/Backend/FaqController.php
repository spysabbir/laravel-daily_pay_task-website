<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FaqController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('faq.index') , only:['index', 'show']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('faq.create') , only:['store']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('faq.edit') , only:['edit', 'update']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('faq.destroy'), only:['destroy']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('faq.trash') , only:['trash']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('faq.restore') , only:['restore']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('faq.delete') , only:['delete']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('faq.status') , only:['status']),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Faq::select('faqs.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $faqs = $query->get();

            return DataTables::of($faqs)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 'Active') {
                        $status = '
                        <span class="badge bg-success">' . $row->status . '</span>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-warning btn-xs statusBtn">Deactive</button>
                        ';
                    } else {
                        $status = '
                        <span class="badge text-white bg-warning">' . $row->status . '</span>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-success btn-xs statusBtn">Active</button>
                        ';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button type="button" data-id="' . $row->id . '" class="btn btn-primary btn-xs editBtn" data-bs-toggle="modal" data-bs-target=".editModal">Edit</button>
                        <button type="button" data-id="' . $row->id . '" class="btn btn-danger btn-xs deleteBtn">Delete</button>
                        ';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.faq.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Faq::create([
                'question' => $request->question,
                'answer' => $request->answer,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $faq = Faq::where('id', $id)->first();
        return response()->json($faq);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            Faq::where('id', $id)->update([
                'question' => $request->question,
                'answer' => $request->answer,
                'updated_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = Faq::onlyTrashed();

            $trashFaq = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashFaq)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '
                        <button type="button" data-id="'.$row->id.'" class="btn bg-success btn-xs restoreBtn">Restore</button>
                        <button type="button" data-id="'.$row->id.'" class="btn bg-danger btn-xs forceDeleteBtn">Delete</button>
                    ';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.faq.index');
    }

    public function restore(string $id)
    {
        Faq::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        Faq::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $faq = Faq::onlyTrashed()->where('id', $id)->first();
        $faq->forceDelete();
    }

    public function status(string $id)
    {
        $faq = Faq::findOrFail($id);

        if ($faq->status == "Active") {
            $faq->status = "Inactive";
        } else {
            $faq->status = "Active";
        }

        $faq->updated_by = auth()->user()->id;
        $faq->save();
    }
}
