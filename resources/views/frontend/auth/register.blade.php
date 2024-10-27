@extends('layouts.frontend')

@section('title', 'Sign Up')

@section('content')
<!-- Sign up Section Start -->
<div class="signup-section ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 offset-md-2 offset-lg-3">
                <form class="signup-form" action="{{ route('register') }}" method="POST">
                    @csrf
                    <input type="hidden" name="referral_code" value="{{ $referral_code }}">
                    <div class="form-group">
                        <label>Enter Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Your Name" name="name" value="{{ old('name') }}" required>
                        <small class="text-info d-block">The name must be as per your verification document (e.g. NID, Passport, Driving License).</small>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Enter Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" placeholder="Enter Your Email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Enter Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" placeholder="Enter Your Password" name="password" required>
                        <small class="text-info d-block">The password must contain at least one lowercase letter, one uppercase letter, one digit, and one special character.</small>
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Enter Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" placeholder="Enter Your Confirm Password" name="password_confirmation" required>
                        @error('password_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group text-center">
                        <div class="d-flex justify-content-center align-items-center">
                            {!! NoCaptcha::display() !!}
                        </div>
                        @error('g-recaptcha-response')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group text-center">
                        <input type="checkbox" class="form-check-input" id="termsConditions" name="terms_conditions" required>
                        <label class="form-check-label" for="termsConditions">
                            I agree to the <a href="{{ route('terms.and.conditions') }}" class="text-primary">terms and conditions.</a>
                        </label>
                        @error('terms_conditions')
                            <span class="text-danger d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="signup-btn text-center">
                        <button type="submit">Sign Up</button>
                    </div>

                    <div class="create-btn text-center">
                        <p>
                            Have an Account?
                            <a href="{{ route('login') }}">
                                Sign In
                                <i class='bx bx-chevrons-right bx-fade-right'></i>
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Sign Up Section End -->
@endsection
