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
                    <div class="text-center mb-4">
                        <strong class="text-info">Note: The all fields must be as per your verification document (e.g. NID or Passport or Driving License)</strong>
                    </div>
                    <input type="hidden" name="referral_code" value="{{ $referral_code }}">
                    <div class="form-group">
                        <label>Enter Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Your Name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Enter Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" placeholder="Enter Your Date of Birth" required>
                        @error('date_of_birth')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <small class="text-danger d-block" id="date_of_birth_error"></small>
                    </div>
                    <div class="form-group">
                        <label>Gender <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Male" name="gender" id="Male" @checked(old('gender') === 'Male') required>
                                <label class="form-check-label" for="Male">
                                    Male
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Female" name="gender" id="Female" @checked(old('gender') === 'Female') required>
                                <label class="form-check-label" for="Female">
                                    Female
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" value="Other" name="gender" id="Other" @checked(old('gender') === 'Other') required>
                                <label class="form-check-label" for="Other">
                                    Other
                                </label>
                            </div>
                        </div>
                        @error('gender')
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
                    {{-- <div class="form-group text-center">
                        <div class="d-flex justify-content-center align-items-center">
                            {!! NoCaptcha::display() !!}
                        </div>
                        @error('g-recaptcha-response')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> --}}
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle password visibility
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

        // Set the max date of birth to today
        const dateInput = document.getElementById('date_of_birth');
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('max', today);
        dateInput.addEventListener('change', function () {
            if (this.value > today) {
                document.getElementById('date_of_birth_error').textContent = 'Future dates are not allowed';
                this.value = '';
            } else {
                document.getElementById('date_of_birth_error').textContent = '';
            }
        });
    });
</script>
