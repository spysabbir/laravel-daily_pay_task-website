<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\JobPost;

class FrontendController extends Controller
{
    public function index()
    {
        $popularJobPostCategories = Category::where('status', 'Active')->limit(8)->get();

        $latestJobPosts = JobPost::where('status', 'Running')->orderBy('id', 'desc')->limit(6)->get();

        $testimonials = Testimonial::where('status', 'Active')->get();

        return view('frontend/index', compact('popularJobPostCategories', 'latestJobPosts', 'testimonials'));
    }

    public function aboutUs()
    {
        return view('frontend/about-us');
    }

    public function contactUs()
    {
        return view('frontend/contact-us');
    }

    public function faq()
    {
        return view('frontend/faq');
    }

    public function howItWorks()
    {
        return view('frontend/how-it-works');
    }

    public function referralProgram()
    {
        return view('frontend/referral-program');
    }

    public function privacyPolicy()
    {
        return view('frontend/privacy-policy');
    }

    public function termsAndConditions()
    {
        return view('frontend/terms-and-conditions');
    }

    public function liveChat()
    {
        return view('frontend/live-chat');
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,email|regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/',
        ],
        [
            'email.regex' => 'The email must follow the format " ****@****.*** ".',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Subscriber::create([
                'email' => $request->email,
            ]);
            return response()->json([
                'status' => 200,
            ]);
        }
    }
}
