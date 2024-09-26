@extends('layouts.frontend')

@section('title', 'Forgot Password')

@section('content')
<!-- Forgot Password Section Start -->
<div class="reset-password ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 offset-md-2 offset-lg-3">
                @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
                @endif
                <form class="reset-form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label>Enter Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" placeholder="Enter Your Email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="reset-btn text-center">
                        <button type="submit">Send Email Password Reset Link</button>
                    </div>
                    <div class="create-btn text-center mt-3">
                        <a href="{{ route('login') }}">
                            Back to Sign In
                            <i class='bx bx-chevrons-right bx-fade-right'></i>
                        </a>
                    </div>
                </form>
            </div>  
        </div>
    </div>
</div>
<!-- Forgot Password Section End -->
@endsection
