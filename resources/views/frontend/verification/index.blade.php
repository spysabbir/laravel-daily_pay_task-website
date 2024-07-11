@extends('layouts.template_master')

@section('title', 'Verification')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        @if (session('error'))
        <div class="alert alert-warning alert-dismissible fade show text-center" role="alert">
            <div class="alert-heading mb-3">
                <i data-feather="alert-circle"></i>
                <h4>Account Not Verified!</h4>
            </div>
            <p class="mt-3">
                Your account is not verified yet. Please verify your account to access all features. Otherwise, you can't access all features. Verifaication account is mandatory to access all features. Need your real id example NID, Passport, Driving License to verify your account. If you have any issue, please contact with us. We are always ready to help you.
            </p>
            <hr>
            <div class="mb-0">
                <a href="javascript:;" class="btn btn-danger btn-sm">Contact Us</a>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
        </div>
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
                    <strong>Note:</strong> Before verification, Please make sure you have fill up your profile information correctly. <br>
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
                        Remarks: {{ $verification->remarks }} <br>
                        <p>
                            <strong>Note:</strong> Please re-submit the verification.
                        </p>
                    </div>
                    @endif
                    <form class="forms-sample" action="{{ route('verification.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            {{-- id_type --}}
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_type" class="form-label">Id Type</label>
                                    <select class="form-select" id="id_type" name="id_type">
                                        <option value="">Select Id Type</option>
                                        <option value="NID" {{ old('id_type') === 'NID' ? 'selected' : '' }}>NID</option>
                                        <option value="Passport" {{ old('id_type') === 'Passport' ? 'selected' : '' }}>Passport</option>
                                        <option value="Driving License" {{ old('id_type') === 'Driving License' ? 'selected' : '' }}>Driving License</option>
                                    </select>
                                </div>
                                @error('id_type')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_number" class="form-label">Id Number</label>
                                    <input type="text" class="form-control" id="id_number" name="id_number" value="{{ old('id_number') }}" placeholder="Id Number">
                                </div>
                                @error('id_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_front_image" class="form-label">Id Front Image</label>
                                    <input type="file" class="form-control" id="id_front_image" name="id_front_image" accept=".jpg, .jpeg, .png">
                                </div>
                                @error('id_front_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="id_with_face_image" class="form-label">Id With Face Image</label>
                                    <input type="file" class="form-control" id="id_with_face_image" name="id_with_face_image" accept=".jpg, .jpeg, .png">
                                </div>
                                @error('id_with_face_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                        </div><!-- Row -->
                        <div class="row mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection
