<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend/index');
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
}
