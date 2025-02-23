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
                    <div class="form-group">
                        <label>Enter Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Enter Your Confirm Password" id="password_confirmation" name="password_confirmation" required>
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirmation">
                                <i class="bx bx-show"></i>
                            </button>
                        </div>
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

@section('script')
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
@endsection

