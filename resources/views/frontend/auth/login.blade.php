@extends('layouts.frontend')

@section('title', 'Sign In')

@section('content')
<!-- Sign In Section Start -->
<div class="signin-section ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-8 offset-md-2 offset-lg-3">
                @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
                @endif
                <form class="signin-form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label>Enter Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" placeholder="Enter Your Email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Enter Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Enter Your Password" id="password" name="password" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                                <i class="bx bx-show"></i>
                            </button>
                        </div>
                        <small class="text-info d-block">The password must be at least 8 characters long and contain upper- and lower-case letters, numbers, and symbols.</small>
                        @error('password')
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
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                        <label class="form-check-label" for="remember_me">
                            Remember me
                        </label>
                    </div>

                    <div class="signin-btn text-center">
                        <button type="submit">Sign In</button>
                    </div>
                    <div class="text-center">
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="btn text-info btn-icon-text mb-2 mb-md-0">
                            Forgot your password?
                        </a>
                        @endif
                    </div>

                    {{-- <div class="other-signin text-center">
                        <span>Or sign in with</span>
                        <ul>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-google'></i>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class='bx bxl-facebook'></i>
                                </a>
                            </li>
                        </ul>
                    </div> --}}

                    <div class="create-btn text-center">
                        <p>Not have an account?
                            <a href="{{ route('register') }}">
                                Create an account
                                <i class='bx bx-chevrons-right bx-fade-right'></i>
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Sign In Section End -->
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bx-show');
                    icon.classList.add('bx-hide');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bx-hide');
                    icon.classList.add('bx-show');
                }
            });
        });
    });
</script>
