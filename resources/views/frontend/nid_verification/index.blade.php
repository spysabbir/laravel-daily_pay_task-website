@extends('layouts.template_master')

@section('title', 'Nid Verification')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Nid Verification</h3>
            </div>
            <div class="card-body">
                @if ($nidVerification && $nidVerification->status !== 'Rejected')
                    @if ($nidVerification->status === 'Pending')
                        <div class="alert alert-warning">
                            Your Nid Verification is Pending
                        </div>
                    @elseif ($nidVerification->status === 'Approved')
                        <div class="alert alert-success">
                            Your Nid Verification is Approved
                        </div>
                    @endif
                @else
                    @if ($nidVerification && $nidVerification->status === 'Rejected')
                    <div class="alert alert-danger">
                        Your Nid Verification is Rejected <br>
                        Remarks: {{ $nidVerification->remarks }} <br>
                        Please try again with correct information and images.
                    </div>
                    @endif
                    <form class="forms-sample" action="{{ route('nid.verification.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label for="nid_number" class="form-label">Nid Number</label>
                                    <input type="text" class="form-control" id="nid_number" name="nid_number" value="{{ old('nid_number') }}" placeholder="Nid Number">
                                </div>
                                @error('nid_number')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label for="nid_date_of_birth" class="form-label">Nid Date Of Birth</label>
                                    <input type="date" class="form-control" id="nid_date_of_birth" name="nid_date_of_birth" value="{{ old('nid_date_of_birth') }}" placeholder="Nid Date Of Birth">
                                </div>
                                @error('nid_date_of_birth')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label for="nid_front_image" class="form-label">Nid Front Image</label>
                                    <input type="file" class="form-control" id="nid_front_image" name="nid_front_image" accept=".jpg, .jpeg, .png">
                                </div>
                                @error('nid_front_image')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div><!-- Col -->
                            <div class="col-lg-3 col-md-6">
                                <div class="mb-3">
                                    <label for="nid_with_face_image" class="form-label">Nid With Face Image</label>
                                    <input type="file" class="form-control" id="nid_with_face_image" name="nid_with_face_image" accept=".jpg, .jpeg, .png">
                                </div>
                                @error('nid_with_face_image')
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
