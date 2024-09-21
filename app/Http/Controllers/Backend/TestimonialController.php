<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Testimonial::select('testimonials.*');

            $query->orderBy('created_at', 'desc');

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $testimonials = $query->get();

            return DataTables::of($testimonials)
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

        return view('backend.testimonial.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'comment' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            $manager = new ImageManager(new Driver());
            $testimonial_photo_name = "Testimonial-Photo-". date('YmdHis') .".". $request->file('photo')->getClientOriginalExtension();
            $image = $manager->read($request->file('photo'));
            $image->scale(width: 500, height: 475);
            $image->toJpeg(80)->save(base_path("public/uploads/testimonial_photo/").$testimonial_photo_name);

            Testimonial::create([
                'photo' => $testimonial_photo_name,
                'name' => $request->name,
                'designation' => $request->designation,
                'comment' => $request->comment,
                'created_by' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function edit(string $id)
    {
        $testimonial = Testimonial::where('id', $id)->first();
        return response()->json($testimonial);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'error' => $validator->errors()->toArray()
            ]);
        } else {
            $testimonial = Testimonial::findOrFail($id);

            if ($request->hasFile('photo')) {
                unlink(public_path('uploads/testimonial_photo/' . $testimonial->photo));
                $manager = new ImageManager(new Driver());
                $testimonial_photo_name = "Testimonial-Photo-" . date('YmdHis') . "." . $request->file('photo')->getClientOriginalExtension();
                $image = $manager->read($request->file('photo'));
                $image->scale(width: 500, height: 475);
                $image->toJpeg(80)->save(base_path("public/uploads/testimonial_photo/") . $testimonial_photo_name);

                $testimonial->photo = $testimonial_photo_name;
            }

            $testimonial->name = $request->name;
            $testimonial->designation = $request->designation;
            $testimonial->comment = $request->comment;
            $testimonial->updated_by = auth()->user()->id;
            $testimonial->save();

            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function destroy(string $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $testimonial->updated_by = Auth::user()->id;
        $testimonial->deleted_by = Auth::user()->id;
        $testimonial->save();
        $testimonial->delete();
    }

    public function trash(Request $request)
    {
        if ($request->ajax()) {
            $query = Testimonial::onlyTrashed();

            $trashTestimonials = $query->orderBy('deleted_at', 'desc')->get();

            return DataTables::of($trashTestimonials)
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

        return view('backend.testimonial.index');
    }

    public function restore(string $id)
    {
        Testimonial::onlyTrashed()->where('id', $id)->update([
            'deleted_by' => NULL
        ]);

        Testimonial::onlyTrashed()->where('id', $id)->restore();
    }

    public function delete(string $id)
    {
        $testimonial = Testimonial::onlyTrashed()->where('id', $id)->first();
        unlink(public_path('uploads/testimonial_photo/' . $testimonial->photo));
        $testimonial->forceDelete();
    }

    public function status(string $id)
    {
        $testimonial = Testimonial::findOrFail($id);

        if ($testimonial->status == "Active") {
            $testimonial->status = "Inactive";
        } else {
            $testimonial->status = "Active";
        }

        $testimonial->updated_by = auth()->user()->id;
        $testimonial->save();
    }
}
