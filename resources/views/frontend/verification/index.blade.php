@extends('layouts.template_master')

@section('title', 'Verification')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        @if (session('error'))
            @if (Auth::user()->hasVerification('Pending'))
            <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
                <div class="alert-heading mb-3">
                    <i data-feather="alert-circle"></i>
                    <h4>Account Verification Pending!</h4>
                </div>
                <p class="mt-3">
                    Your account verification is pending. Please wait for admin approval. Admin will verify your account as soon as possible. If you have any issue, please contact with us. We are always ready to help you.
                </p>
                <hr>
                <div class="mb-0">
                    <a href="{{ route('support') }}" class="btn btn-primary btn-sm">Contact Us</a>
                </div>
            </div>
            @elseif (Auth::user()->hasVerification('Rejected'))
            <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                <div class="alert-heading mb-3">
                    <i data-feather="alert-circle"></i>
                    <h4>Account Verification Rejected!</h4>
                </div>
                <p class="mt-3">
                    Your account verification is rejected by admin. Please contact with us to re-verify your account. We are always ready to help you.
                </p>
                <hr>
                <div class="mb-0">
                    <a href="{{ route('support') }}" class="btn btn-primary btn-sm">Contact Us</a>
                </div>
            </div>
            @elseif (Auth::user()->hasVerification('Approved'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <div class="alert-heading mb-3">
                    <i data-feather="check-circle"></i>
                    <h4>Account Verified!</h4>
                </div>
            </div>
            @else
            <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
                <div class="alert-heading mb-3">
                    <i data-feather="alert-circle"></i>
                    <h4>Account Verification Required!</h4>
                </div>
                <p class="mt-3">
                    Your account verification is required. Please verify your account to access your account. If you have any issue, please contact with us. We are always ready to help you.
                </p>
                <hr>
                <div class="mb-0">
                    <a href="{{ route('support') }}" class="btn btn-primary btn-sm">Contact Us</a>
                </div>
            </div>
            @endif
        @endif
    </div>
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Verification</h3>
                @if ($verification && $verification->status !== 'Rejected')
                <p class="text-info">
                    <strong>Note:</strong> You have already submitted the verification. <br>
                </p>
                @else
                <p class="text-warning">
                    <strong>Note:</strong> Before verification, Please make sure you have fill up your profile information correctly. As per your verification id, your profile information should be matched.
                </p>
                @endif
            </div>
            <div class="card-body">
                @if ($verification && $verification->status !== 'Rejected')
                    @if ($verification->status === 'Pending')
                        <div class="alert alert-warning">
                            Your Verification is Pending
                            <p>
                                <strong>Note:</strong> Please wait for the approval.
                            </p>
                        </div>
                    @elseif ($verification->status === 'Approved')
                        <div class="alert alert-success">
                            Your Verification is Approved
                            <p>
                                <strong>Note:</strong> You can now use all the features.
                            </p>
                        </div>
                    @endif
                @else
                    @if ($verification && $verification->status === 'Rejected')
                    <div class="alert alert-danger">
                        Your Verification is Rejected <br>
                        Rejected Reason: {{ $verification->rejected_reason }} <br>
                        <p>
                            <strong>Note:</strong> Please re-submit the verification.
                        </p>
                    </div>
                    @endif
                    <form class="forms-sample" action="{{ $verification && $verification->status === 'Rejected' ? route('re-verification.store') : route('verification.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if ($verification && $verification->status === 'Rejected')
                            <input type="hidden" name="verification_id" value="{{ $verification->id }}">
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_type" class="form-label">Id Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_type" name="id_type" required>
                                        <option value="">Select Id Type</option>
                                        <option value="NID" {{ old('id_type', $verification->id_type ?? '') === 'NID' ? 'selected' : '' }}>NID</option>
                                        <option value="Passport" {{ old('id_type', $verification->id_type ?? '') === 'Passport' ? 'selected' : '' }}>Passport</option>
                                        <option value="Driving License" {{ old('id_type', $verification->id_type ?? '') === 'Driving License' ? 'selected' : '' }}>Driving License</option>
                                    </select>
                                </div>
                                @error('id_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_number" class="form-label">Id Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="id_number" name="id_number" value="{{ old('id_number', $verification->id_number ?? '') }}" placeholder="Enter Id Number" required>
                                </div>
                                @error('id_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_front_image" class="form-label">Id Front Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="id_front_image" name="id_front_image" accept=".jpg, .jpeg, .png">
                                    <small class="text-info">Id Front Image must be a valid image file (jpeg, jpg, png) and the file size must be less than 2MB.</small>
                                    <span id="id_front_imageError" class="text-danger d-block"></span>
                                    <img src="{{ asset('uploads/verification_photo') }}/{{ $verification->id_front_image ?? '' }}" id="id_front_image_preview" class="img-fluid mt-3" style="height: 280px; display: {{ $verification && $verification->id_front_image ? 'block' : 'none' }};">
                                </div>
                                @error('id_front_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_with_face_image" class="form-label">Id With Face Image <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="id_with_face_image" name="id_with_face_image" accept=".jpg, .jpeg, .png">
                                    <small class="text-info">Id With Face Image must be a valid image file (jpeg, jpg, png) and the file size must be less than 2MB.</small>
                                    <span id="id_with_face_imageError" class="text-danger d-block"></span>
                                    <img src="{{ asset('uploads/verification_photo') }}/{{ $verification->id_with_face_image ?? '' }}" id="id_with_face_image_preview" class="img-fluid mt-3" style="height: 280px; display: {{ $verification && $verification->id_with_face_image ? 'block' : 'none' }};">
                                </div>
                                @error('id_with_face_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mt-3">
                            <button type="submit" class="btn btn-primary">{{ $verification && $verification->status === 'Rejected' ? 'Re-Submit' : 'Submit' }}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {

        // Id Front Image Preview
        document.getElementById('id_front_image').addEventListener('change', function() {
            const file = this.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file && allowedTypes.includes(file.type)) {
                if (file.size > maxSize) {
                    $('#id_front_imageError').text('File size is too large. Max size is 2MB.');
                    this.value = ''; // Clear file input
                    // Hide preview image
                    $('#id_front_image_preview').hide();
                } else {
                    $('#id_front_imageError').text('');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#id_front_image_preview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                $('#id_front_imageError').text('Please select a valid image file (jpeg, jpg, png).');
                this.value = ''; // Clear file input
                // Hide preview image
                $('#id_front_image_preview').hide();
            }
        });

        // Id With Face Image Preview
        document.getElementById('id_with_face_image').addEventListener('change', function() {
            const file = this.files[0];
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            const maxSize = 2 * 1024 * 1024; // 2MB

            if (file && allowedTypes.includes(file.type)) {
                if (file.size > maxSize) {
                    $('#id_with_face_imageError').text('File size is too large. Max size is 2MB.');
                    this.value = ''; // Clear file input
                    // Hide preview image
                    $('#id_with_face_image_preview').hide();
                } else {
                    $('#id_with_face_imageError').text('');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#id_with_face_image_preview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(file);
                }
            } else {
                $('#id_with_face_imageError').text('Please select a valid image file (jpeg, jpg, png).');
                this.value = ''; // Clear file input
                // Hide preview image
                $('#id_with_face_image_preview').hide();
            }
        });
    });
</script>
@endsection
