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
                @if ($nidVerification)
                    @if ($nidVerification->status === 'Pending')
                        <div class="alert alert-warning">
                            Your Nid Verification is Pending
                        </div>
                    @elseif ($nidVerification->status === 'Approved')
                        <div class="alert alert-success">
                            Your Nid Verification is Approved
                        </div>
                    @else
                    <div class="alert alert-danger">
                        Your Nid Verification is Rejected
                    </div>
                    @endif
                @else
                <form class="forms-sample" action="{{ route('nid.verification.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="nid_number" class="form-label">Nid Number</label>
                                <input type="text" class="form-control" id="nid_number" name="nid_number" value="{{ old('nid_number') }}" placeholder="Nid Number">
                            </div>
                            @error('nid_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="nid_front_image" class="form-label">Nid Front Image</label>
                                <input type="file" class="form-control" id="nid_front_image" name="nid_front_image">
                            </div>
                            @error('nid_front_image')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="nid_with_face_image" class="form-label">Nid With Face Image</label>
                                <input type="file" class="form-control" id="nid_with_face_image" name="nid_with_face_image">
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
