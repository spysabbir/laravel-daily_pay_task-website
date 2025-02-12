@extends('layouts.template_master')

@section('title', 'Sms Setting')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Sms Setting</h3>
                <p><span class="text-danger">*</span> <small>Fields are required</small></p>
            </div>
            <div class="card-body">
                <form class="forms-sample" action="{{ route('backend.sms.setting.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label for="sms_driver" class="form-label">Sms Driver <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sms_driver" name="sms_driver" value="{{ old('sms_driver', $smsSetting->sms_driver) }}" placeholder="Sms Driver">
                            @error('sms_driver')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label for="sms_api_key" class="form-label">Sms Api Key <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sms_api_key" name="sms_api_key" value="{{ old('sms_api_key', $smsSetting->sms_api_key) }}" placeholder="Sms Api Key">
                            @error('sms_api_key')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-4 col-sm-6 mb-3">
                            <label for="sms_send_from" class="form-label">Sms Send From <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sms_send_from" name="sms_send_from" value="{{ old('sms_send_from', $smsSetting->sms_send_from) }}" placeholder="Sms Send From">
                            @error('sms_send_from')
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
