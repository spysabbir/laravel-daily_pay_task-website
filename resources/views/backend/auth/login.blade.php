@extends('layouts.auth_master')

@section('title', 'Login')

@section('content')
<div class="row">
    <div class="col-md-4 pe-md-0">
        <div class="auth-side-wrapper">

        </div>
    </div>
    <div class="col-md-8 ps-md-0">
        <div class="auth-form-wrapper px-4 py-5">
            <span class="noble-ui-logo logo-light d-block mb-2"><span>{{ config('app.name') }}</span></span>
            <h5 class="text-muted fw-normal mb-4">Welcome back! Log in to your account.</h5>
            @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
            @endif
            <form class="forms-sample" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="userEmail" class="form-label">Email address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="userEmail" name="email" value="superadmin@spysabbir.com" placeholder="Email" required>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="userPassword" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="userPassword" value="Ss@12345678" name="password" placeholder="Password">
                        <button type="button" class="input-group-text input-group-addon toggle-password" data-target="userPassword" required>
                            <span class="icon">🔒</span>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    {!! NoCaptcha::display() !!}
                    @error('g-recaptcha-response')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                    <label class="form-check-label" for="remember_me">
                        Remember me
                    </label>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0 text-white">Login</button>
                    @if (Route::has('password.request'))
                    <a href="{{ route('backend.password.request') }}" class="btn text-primary btn-icon-text mb-2 mb-md-0">
                        Forgot your password?
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('.icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.textContent = '👁️'; // Change to "eye" icon
                } else {
                    input.type = 'password';
                    icon.textContent = '🔒'; // Change to "eye-off" icon
                }
            });
        });
    });
</script>
