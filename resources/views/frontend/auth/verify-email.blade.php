@extends('layouts.frontend')

@section('title', 'Verify Email')

@section('content')
<!-- Verify Email Section Start -->
<div class="reset-password ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 offset-md-2 offset-lg-3">
                @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
                @endif
                <form class="reset-form" method="POST" action="{{ route('verification.send') }}">
                    <p class="text-success">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.</p>
                    @csrf
                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Resend Verification Email</button>
                    </div>
                </form>
                <form class="reset-form mt-3" method="POST" action="{{ route('logout') }}">
                    <p>
                        If you are not using your own device, please remember to log out after you have finished using our services to protect your personal information.
                    </p>
                    @csrf
                    <div class="text-center">
                        <button type="submit" class="btn btn-danger">Log Out</button>
                    </div>
                </form>
            </div>  
        </div>
    </div>
</div>
<!-- Verify Email Section End -->
@endsection

