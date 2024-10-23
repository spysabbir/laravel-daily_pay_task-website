@extends('layouts.frontend')

@section('title', 'Reset Password')

@section('content')
<!-- Reset Password Section Start -->
<div class="reset-password ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 offset-md-2 offset-lg-3">
                <form class="signup-form" method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <input type="hidden" name="email" value="{{ $request->email }}">
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
                    <div class="signup-btn text-center">
                        <button type="submit">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Reset Password Section End -->
@endsection
