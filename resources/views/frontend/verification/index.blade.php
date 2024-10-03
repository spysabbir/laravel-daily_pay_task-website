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
                    <a href="javascript:;" class="btn btn-primary btn-sm">Contact Us</a>
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
                    <a href="javascript:;" class="btn btn-primary btn-sm">Contact Us</a>
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
                    <a href="javascript:;" class="btn btn-primary btn-sm">Contact Us</a>
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
                                    <label for="id_type" class="form-label">Id Type</label>
                                    <select class="form-select" id="id_type" name="id_type">
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
                                    <label for="id_number" class="form-label">Id Number</label>
                                    <input type="text" class="form-control" id="id_number" name="id_number" value="{{ old('id_number', $verification->id_number ?? '') }}" placeholder="Enter Id Number">
                                </div>
                                @error('id_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_front_image" class="form-label">Id Front Image</label>
                                    <input type="file" class="form-control" id="id_front_image" name="id_front_image" accept=".jpg, .jpeg, .png">
                                    <img src="{{ asset('uploads/verification_photo') }}/{{ $verification->id_front_image ?? '' }}" id="id_front_image_preview" class="img-fluid mt-3" style="height: 280px; display: {{ $verification && $verification->id_front_image ? 'block' : 'none' }};">
                                </div>
                                @error('id_front_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_with_face_image" class="form-label">Id With Face Image</label>
                                    <input type="file" class="form-control" id="id_with_face_image" name="id_with_face_image" accept=".jpg, .jpeg, .png">
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
        // Image Preview
        $('#id_front_image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#id_front_image_preview').attr('src', e.target.result).css('display', 'block');
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('#id_with_face_image').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#id_with_face_image_preview').attr('src', e.target.result).css('display', 'block');
            }
            reader.readAsDataURL(this.files[0]);
        });
    });
</script>
@endsection
