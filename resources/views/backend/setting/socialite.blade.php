@extends('layouts.template_master')

@section('title', 'Socialite Setting')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Socialite Setting</h3>
                <p><span class="text-danger">*</span> <small>Fields are required</small></p>
            </div>
            <div class="card-body">
                <form class="forms-sample" action="{{ route('backend.socialite.setting.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label for="google_client_id" class="form-label">Google Client Id <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="google_client_id" name="google_client_id" value="{{ old('google_client_id', $socialiteSetting->google_client_id) }}" placeholder="Google Client Id">
                            @error('google_client_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-6 mb-3">
                            <label for="google_client_secret" class="form-label">Google Client Secret <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="google_client_secret" name="google_client_secret" value="{{ old('google_client_secret', $socialiteSetting->google_client_secret) }}" placeholder="Google Client Secret">
                            @error('google_client_secret')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-6 mb-3">
                            <label for="facebook_client_id" class="form-label">Facebook Client Id <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="facebook_client_id" name="facebook_client_id" value="{{ old('facebook_client_id', $socialiteSetting->facebook_client_id) }}" placeholder="Facebook Client Id">
                            @error('facebook_client_id')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-6 mb-3">
                            <label for="facebook_client_secret" class="form-label">Facebook Client Secret <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="facebook_client_secret" name="facebook_client_secret" value="{{ old('facebook_client_secret', $socialiteSetting->facebook_client_secret) }}" placeholder="Facebook Client Secret">
                            @error('facebook_client_secret')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                    </div><!-- Row -->
                    <div class="row mt-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
