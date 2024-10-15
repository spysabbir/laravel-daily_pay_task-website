@extends('layouts.template_master')

@section('title', 'Captcha Setting')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Captcha Setting</h3>
            </div>
            <div class="card-body">
                <form class="forms-sample" action="{{ route('backend.captcha.setting.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label for="captcha_secret_key" class="form-label">Captcha Secret Key</label>
                            <input type="text" class="form-control" id="captcha_secret_key" name="captcha_secret_key" value="{{ old('captcha_secret_key', $captchaSetting->captcha_secret_key) }}" placeholder="Captcha Secret Key">
                            @error('captcha_secret_key')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label for="captcha_site_key" class="form-label">Captcha Site Key</label>
                            <input type="text" class="form-control" id="captcha_site_key" name="captcha_site_key" value="{{ old('captcha_site_key', $captchaSetting->captcha_site_key) }}" placeholder="Captcha Site Key">
                            @error('captcha_site_key')
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
