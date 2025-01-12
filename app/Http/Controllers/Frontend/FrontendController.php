<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\PostTask;
use App\Models\User;
use App\Models\Withdraw;

class FrontendController extends Controller
{
    public function index()
    {

        $latestPostTasks = PostTask::where('status', 'Running')->orderBy('approved_at', 'desc')->limit(6)->get();
        $testimonials = Testimonial::where('status', 'Active')->latest()->limit(6)->get();
        $topCategories = PostTask::groupBy('category_id')->selectRaw('count(*) as count, category_id')->orderBy('count', 'desc')->limit(6)->with('category')->get();
        $topBuyers = PostTask::groupBy('user_id')->selectRaw('count(*) as count, user_id')->orderBy('count', 'desc')->limit(6)->with('user')->get();

        $totalPostTask = PostTask::count();
        $runningPostTasks = PostTask::where('status', 'Running')->count();
        $totalUser = User::where('user_type', 'Frontend')->count();
        $totalWithdraw = Withdraw::where('status', 'Approved')->where('method', 'Deposit Balance')->sum('amount');

        return view('frontend/index', compact('latestPostTasks', 'testimonials', 'topCategories', 'topBuyers', 'totalPostTask', 'runningPostTasks', 'totalUser', 'totalWithdraw'));
    }

    public function aboutUs()
    {
        $testimonials = Testimonial::where('status', 'Active')->latest()->limit(6)->get();
        $totalPostTask = PostTask::count();
        $runningPostTask = PostTask::where('status', 'Running')->count();
        $totalUser = User::where('user_type', 'Frontend')->count();
        $totalWithdraw = Withdraw::where('status', 'Approved')->where('method', 'Deposit Balance')->sum('amount');

        return view('frontend/about-us', compact('testimonials', 'totalPostTask', 'runningPostTask', 'totalUser', 'totalWithdraw'));
    }

    public function contactUs()
    {
        return view('frontend/contact-us');
    }

    public function faq()
    {
        $faqs = Faq::where('status', 'Active')->get();

        return view('frontend/faq', compact('faqs'));
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

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscribe_email' => 'required|email|unique:subscribers,email|regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/',
        ],
        [
            'subscribe_email.regex' => 'The email must follow the format " ****@****.*** ".',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Subscriber::create([
                'email' => $request->subscribe_email,
            ]);
            return response()->json([
                'status' => 200,
            ]);
        }
    }

    public function unSubscribe(string $id)
    {
        $id = decrypt($id);
        $subscriber = Subscriber::where('id', $id)->first();
        if ($subscriber) {
            $subscriber->update(['status' => 'Inactive']);

            return response()->json(['status' => 200, 'message' => 'You have successfully unsubscribed']);
        }

        return response()->json(['status' => 400, 'message' => 'Invalid email address']);
    }

    public function contactStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|max:255|email|regex:/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]+$/',
            'phone' => ['nullable', 'string', 'regex:/^(?:\+8801|01)[3-9]\d{8}$/'],
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ],
        [
            'email.regex' => 'The email must follow the format " ****@****.*** ".',
            'phone.regex' => 'The phone number must be a valid Bangladeshi number (+8801XXXXXXXX or 01XXXXXXXX).',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'error'=> $validator->errors()->toArray()
            ]);
        }else{
            Contact::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);
            return response()->json([
                'status' => 200,
            ]);
        }
    }
}
