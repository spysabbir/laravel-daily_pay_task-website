<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\JobPostCharge;
use App\Models\JobPost;
use Illuminate\Contracts\Queue\Job;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class PostJobController extends Controller
{
    public function postJob()
    {
        $user = User::findOrFail(Auth::id());
        $hasVerification = $user->hasVerification('Approved');

        if (!$hasVerification) {
            return redirect()->route('verification')->with('error', 'Please verify your account first.');
        } else if ($user->status == 'Blocked' || $user->status == 'Banned') {
            return redirect()->route('dashboard')->with('error', 'Your account is blocked or banned.');
        } else {
            $categories = Category::where('status', 'Active')->get();
            return view('frontend.post_job.create', compact('categories'));
        }
    }

    public function getSubCategories(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategories = SubCategory::where('category_id', $categoryId)->get();

        $response = [];
        if ($subCategories->isNotEmpty()) {
            $response['sub_categories'] = $subCategories;
        }

        return response()->json($response);
    }

    public function getChildCategories(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;
        $childCategories = ChildCategory::where('sub_category_id', $subCategoryId)->get();

        $response = [];
        if ($childCategories->isNotEmpty()) {
            $response['child_categories'] = $childCategories;
        }

        $response['job_post_charge'] = JobPostCharge::where('category_id', $categoryId)
                                                ->where('sub_category_id', $subCategoryId)
                                                ->first();

        return response()->json($response);
    }

    public function getJobPostCharge(Request $request)
    {
        $categoryId = $request->category_id;
        $subCategoryId = $request->sub_category_id;
        $childCategoryId = $request->child_category_id;

        $charge = JobPostCharge::where('category_id', $categoryId)
            ->where('sub_category_id', $subCategoryId)
            ->where('child_category_id', $childCategoryId)
            ->first();

        return response()->json($charge);
    }

    public function postJobStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_proof' => 'required|string',
            'additional_note' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'need_worker' => 'required|numeric|min:1',
            'worker_charge' => 'required|numeric|min:1',
            'extra_screenshots' => 'required|numeric|min:0',
            'boosted_time' => 'required|numeric|min:0',
            'running_day' => 'required|numeric|min:1',
        ]);

        if($request->hasFile('thumbnail')){
            $manager = new ImageManager(new Driver());
            $thumbnail_photo_name = $request->user()->id."-thumbnail-photo". date('YmdHis') . "." . $request->file('thumbnail')->getClientOriginalExtension();
            $image = $manager->read($request->file('thumbnail'));
            $image->toJpeg(80)->save(base_path("public/uploads/job_thumbnail_photo/").$thumbnail_photo_name);
        }else{
            $thumbnail_photo_name = null;
        }

        $job_post_charge = (($request->need_worker * $request->worker_charge) + ($request->extra_screenshots * get_default_settings('extra_screenshot_charge'))) + (($request->boosted_time / 15) * get_default_settings('job_boosted_charge'));

        $site_charge = $job_post_charge * get_default_settings('job_posting_charge_percentage') / 100;

        $request->user()->update([
            'deposit_balance' => $request->user()->deposit_balance - ($job_post_charge + $site_charge),
        ]);

        JobPost::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'child_category_id' => $request->child_category_id,
            'title' => $request->title,
            'description' => $request->description,
            'required_proof' => $request->required_proof,
            'additional_note' => $request->additional_note,
            'thumbnail' => $thumbnail_photo_name,
            'need_worker' => $request->need_worker,
            'worker_charge' => $request->worker_charge,
            'extra_screenshots' => $request->extra_screenshots,
            'boosted_time' => $request->boosted_time,
            'running_day' => $request->running_day,
            'charge' => $job_post_charge,
            'site_charge' => $site_charge,
            'total_charge' => $job_post_charge + $site_charge,
            'status' => 'Pending',
        ]);

        $notification = array(
            'message' => 'Job post submitted successfully.',
            'alert-type' => 'success'
        );

        return to_route('job.list.pending')->with($notification);
    }

    public function postJobEdit($id)
    {
        $categories = Category::where('status', 'Active')->get();
        $jobPost = JobPost::findOrFail($id);
        return view('frontend.post_job.edit', compact('categories', 'jobPost'));
    }
}
