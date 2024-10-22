@extends('layouts.template_master')

@section('title', 'Seo Setting')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Seo Setting</h3>
            </div>
            <div class="card-body">
                <form class="forms-sample" action="{{ route('backend.seo.setting.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-9 col-sm-12 mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $seoSetting->title) }}" placeholder="Title">
                            @error('title')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-12 mb-3">
                            <label for="author" class="form-label">Author</label>
                            <input type="text" class="form-control" id="author" name="author" value="{{ old('author', $seoSetting->author) }}" placeholder="Author">
                            @error('author')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-12 mb-3">
                            <label for="keywords" class="form-label">Keyword</label>
                            <input type="text" class="form-control" id="keywords" name="keywords" value="{{ old('keywords', $seoSetting->keywords) }}" placeholder="Keywords">
                            @error('keywords')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Description">{{ old('description', $seoSetting->description) }}</textarea>
                            @error('description')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-6 col-sm-12 mb-3">
                            <label for="og_url" class="form-label">Og Url</label>
                            <input type="text" class="form-control" id="og_url" name="og_url" value="{{ old('og_url', $seoSetting->og_url) }}" placeholder="Og Url">
                            @error('og_url')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-6 col-sm-12 mb-3">
                            <label for="og_site_name" class="form-label">Og Site Name</label>
                            <input type="text" class="form-control" id="og_site_name" name="og_site_name" value="{{ old('og_site_name', $seoSetting->og_site_name) }}" placeholder="Og Site Name">
                            @error('og_site_name')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-6 col-sm-12 mb-3">
                            <label for="twitter_card" class="form-label">Twitter Card</label>
                            <input type="text" class="form-control" id="twitter_card" name="twitter_card" value="{{ old('twitter_card', $seoSetting->twitter_card) }}" placeholder="Twitter Card">
                            @error('twitter_card')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-6 col-sm-12 mb-3">
                            <label for="twitter_site" class="form-label">Twitter Site</label>
                            <input type="text" class="form-control" id="twitter_site" name="twitter_site" value="{{ old('twitter_site', $seoSetting->twitter_site) }}" placeholder="Twitter Site">
                            @error('twitter_site')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-6 col-sm-12 mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" name="image" id="image">
                            @error('image')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                            <img width="80" height="80" class="mt-1 rounded" src="{{asset('uploads/setting_photo')}}/{{$seoSetting->image}}" id="imagePreview"  alt="Image">
                        </div>
                        <div class="col-lg-6 col-sm-12 mb-3">
                            <label for="image_alt" class="form-label">Image Alt</label>
                            <input type="text" class="form-control" id="image_alt" name="image_alt" value="{{ old('image_alt', $seoSetting->image_alt) }}" placeholder="Image Alt">
                            @error('image_alt')
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

@section('script')
<script>
    $(document).ready(function(){
        // Image Preview
        $('#image').change(function(){
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#magePreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });
    })
</script>
@endsection
