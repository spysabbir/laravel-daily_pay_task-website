@extends('layouts.auth_master')

@section('title', 'Reset Password')

@section('content')
<div class="row">
    <div class="col-md-4 pe-md-0">
        <div class="auth-side-wrapper">

        </div>
    </div>
    <div class="col-md-8 ps-md-0">
        <div class="auth-form-wrapper px-4 py-5">
            <span class="noble-ui-logo logo-light d-block mb-2"><span>{{ config('app.name') }}</span></span>
            <h5 class="text-muted fw-normal mb-4"></h5>
            <form class="forms-sample" method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="email" value="{{ $request->email }}">
                <div class="mb-3">
                    <label for="userPassword" class="form-label">New Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="userPassword" name="password" placeholder="New Password" required>
                        <button type="button" class="input-group-text input-group-addon toggle-password" data-target="userPassword">
                            <span class="icon">🔒</span>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="userConfirmPassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="userConfirmPassword" name="password_confirmation" placeholder="Confirm Password" required>
                        <button type="button" class="input-group-text input-group-addon toggle-password" data-target="userConfirmPassword">
                            <span class="icon">🔒</span>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <button type="submit" class="btn btn-primary me-2 mb-2 mb-md-0 text-white">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
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
@endsection
