@extends('layouts.template_master')

@section('title', 'Site Setting')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title">Site Setting</h3>
            </div>
            <div class="card-body">
                <form class="forms-sample" action="{{ route('backend.site.setting.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6 mb-3">
                            <label for="site_logo" class="form-label">Site Logo</label>
                            <input type="file" class="form-control" name="site_logo" id="site_logo" accept=".jpg, .jpeg, .png">
                            @error('site_logo')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                            <img width="100" height="80" class="mt-2 rounded" src="{{asset('uploads/setting_photo')}}/{{$siteSetting->site_logo}}" id="site_logoPreview"  alt="Site Logo">
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="site_favicon" class="form-label">Site Favicon</label>
                            <input type="file" class="form-control" name="site_favicon" id="site_favicon" accept=".jpg, .jpeg, .png">
                            @error('site_favicon')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                            <img width="80" height="80" class="mt-2 rounded" src="{{asset('uploads/setting_photo')}}/{{$siteSetting->site_favicon}}" id="site_faviconPreview"  alt="Site Favicon">
                        </div>
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" value="{{ old('site_name', $siteSetting->site_name) }}" placeholder="Site Name">
                            @error('site_name')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_url" class="form-label">Site Url</label>
                            <input type="text" class="form-control" id="site_url" name="site_url" value="{{ old('site_url', $siteSetting->site_url) }}" placeholder="Site Url">
                            @error('site_url')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-2 col-sm-6 mb-3">
                            <label for="site_timezone" class="form-label">Time Zone</label>
                            <select class="form-select" name="site_timezone" id="site_timezone">
                                <option value="">Select Time Zone</option>
                                <option value="UTC" @selected(old('site_timezone', $siteSetting->site_timezone == 'UTC'))>UTC</option>
                                <option value="Asia/Dhaka" @selected(old('site_timezone', $siteSetting->site_timezone == 'Asia/Dhaka'))>Asia/Dhaka</option>
                            </select>
                            @error('site_timezone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-lg-2 col-sm-6 mb-3">
                            <label for="site_currency" class="form-label">Site Currency</label>
                            <select class="form-select" name="site_currency" id="site_currency">
                                <option value="">Select Currency</option>
                                <option value="USD" @selected(old('site_currency', $siteSetting->site_currency == 'USD'))>USD</option>
                                <option value="BDT" @selected(old('site_currency', $siteSetting->site_currency == 'BDT'))>BDT</option>
                            </select>
                            @error('site_currency')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-2 col-sm-6 mb-3">
                            <label for="site_currency_symbol" class="form-label">Site Currency Symbol</label>
                            <select class="form-select" name="site_currency_symbol" id="site_currency_symbol">
                                <option value="">Select Currency Symbol</option>
                                <option value="$" @selected(old('site_currency_symbol', $siteSetting->site_currency_symbol == '$'))>$</option>
                                <option value="৳" @selected(old('site_currency_symbol', $siteSetting->site_currency_symbol == '৳'))>৳</option>
                            </select>
                            @error('site_currency_symbol')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_main_email" class="form-label">Site Main Email</label>
                            <input type="text" class="form-control" id="site_main_email" name="site_main_email" value="{{ old('site_main_email', $siteSetting->site_main_email) }}" placeholder="Site Main Email">
                            @error('site_main_email')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_support_email" class="form-label">Site Support Email</label>
                            <input type="text" class="form-control" id="site_support_email" name="site_support_email" value="{{ old('site_support_email', $siteSetting->site_support_email) }}" placeholder="Site Support Email">
                            @error('site_support_email')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_main_phone" class="form-label">Site Main Phone</label>
                            <input type="text" class="form-control" id="site_main_phone" name="site_main_phone" value="{{ old('site_main_phone', $siteSetting->site_main_phone) }}" placeholder="Site Main Phone">
                            @error('site_main_phone')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_support_phone" class="form-label">Site Support Phone</label>
                            <input type="text" class="form-control" id="site_support_phone" name="site_support_phone" value="{{ old('site_support_phone', $siteSetting->site_support_phone) }}" placeholder="Site Support Phone">
                            @error('site_support_phone')
                                <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div><!-- Col -->
                        <div class="col-lg-12 col-sm-6 mb-3">
                            <label for="site_address" class="form-label">Site Address</label>
                            <textarea class="form-control" id="site_address" name="site_address" rows="4" placeholder="Site Address">{{ old('site_address', $siteSetting->site_address) }}</textarea>
                        </div><!-- Col -->
                        <div class="col-lg-12 col-sm-6 mb-3">
                            <label for="site_notice" class="form-label">Site Notice</label>
                            <textarea class="form-control" id="site_notice" name="site_notice" rows="4" placeholder="Site Notice">{{ old('site_notice', $siteSetting->site_notice) }}</textarea>
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_facebook_url" class="form-label">Site Facebook Url</label>
                            <input type="text" class="form-control" id="site_facebook_url" name="site_facebook_url" value="{{ old('site_facebook_url', $siteSetting->site_facebook_url) }}" placeholder="Site Facebook Url">
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_twitter_url" class="form-label">Site Twitter Url</label>
                            <input type="text" class="form-control" id="site_twitter_url" name="site_twitter_url" value="{{ old('site_twitter_url', $siteSetting->site_twitter_url) }}" placeholder="Site Twitter Url">
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_instagram_url" class="form-label">Site Instagram Url</label>
                            <input type="text" class="form-control" id="site_instagram_url" name="site_instagram_url" value="{{ old('site_instagram_url', $siteSetting->site_instagram_url) }}" placeholder="Site Instagram Url">
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_linkedin_url" class="form-label">Site Linkedin Url</label>
                            <input type="text" class="form-control" id="site_linkedin_url" name="site_linkedin_url" value="{{ old('site_linkedin_url', $siteSetting->site_linkedin_url) }}" placeholder="Site Linkedin Url">
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_pinterest_url" class="form-label">Site Pinterest Url</label>
                            <input type="text" class="form-control" id="site_pinterest_url" name="site_pinterest_url" value="{{ old('site_pinterest_url', $siteSetting->site_pinterest_url) }}" placeholder="Site Pinterest Url">
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_youtube_url" class="form-label">Site Youtube Url</label>
                            <input type="text" class="form-control" id="site_youtube_url" name="site_youtube_url" value="{{ old('site_youtube_url', $siteSetting->site_youtube_url) }}" placeholder="Site Youtube Url">
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_whatsapp_url" class="form-label">Site Whatsapp Url</label>
                            <input type="text" class="form-control" id="site_whatsapp_url" name="site_whatsapp_url" value="{{ old('site_whatsapp_url', $siteSetting->site_whatsapp_url) }}" placeholder="Site Whatsapp Url">
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_telegram_url" class="form-label">Site Telegram Url</label>
                            <input type="text" class="form-control" id="site_telegram_url" name="site_telegram_url" value="{{ old('site_telegram_url', $siteSetting->site_telegram_url) }}" placeholder="Site Telegram Url">
                        </div><!-- Col -->
                        <div class="col-lg-3 col-sm-6 mb-3">
                            <label for="site_tiktok_url" class="form-label">Site Tiktok Url</label>
                            <input type="text" class="form-control" id="site_tiktok_url" name="site_tiktok_url" value="{{ old('site_tiktok_url', $siteSetting->site_tiktok_url) }}" placeholder="Site Tiktok Url">
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
        // Logo Image Preview
        $('#site_logo').change(function(){
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#site_logoPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });
        // Favicon Preview
        $('#site_favicon').change(function(){
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#site_faviconPreview').attr('src', e.target.result).show();
            }
            reader.readAsDataURL(this.files[0]);
        });
    })
</script>
@endsection
