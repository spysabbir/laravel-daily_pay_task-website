@extends('layouts.frontend')

@section('title', 'Home')

@section('content')
<!-- Banner Section Start -->
<div class="banner-style-two">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="container">
                <div class="banner-text">
                    <span>Wellcome to {{ config('app.name') }}</span>
                    <h1>{{ get_site_settings('site_tagline') }}</h1>
                    <p>{{ get_site_settings('site_description') }}</p>
                    <div class="theme-btn">
                        <a href="{{ route('contact.us') }}" class="default-btn">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="banner-img">
        <img src="{{ asset('frontend') }}/img/banner/3.jpg" alt="banner image">
    </div>
</div>
<!-- Banner Section End -->

<!-- Why Choose Section Start -->
<section class="choose-style-two why-choose">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="why-choose-text pt-100 pb-70">
                    <div class="section-title">
                        <h2>Why You Choose {{ config('app.name') }}?</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus</p>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="media">
                                <i class="flaticon-group align-self-top mr-3"></i>
                                <div class="media-body">
                                    <h5 class="mt-0">Best Talented People</h5>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="media">
                                <i class="flaticon-research align-self-top mr-3"></i>
                                <div class="media-body">
                                    <h5 class="mt-0">Easy To Find Canditate</h5>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="media">
                                <i class="flaticon-discussion align-self-top mr-3"></i>
                                <div class="media-body">
                                    <h5 class="mt-0">Easy To Communicate</h5>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="media">
                                <i class="flaticon-recruitment align-self-top mr-3"></i>
                                <div class="media-body">
                                    <h5 class="mt-0">Global Recruitment Option</h5>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-sm-8 offset-sm-2 offset-lg-0">
                <img src="{{ asset('frontend') }}/img/choose.jpg" alt="why choose image">
            </div>
        </div>
    </div>
</section>
<!-- Why Choose Section End -->

<!-- Job Category Section Start -->
<div class="category-style-two pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Popular Jobs Category</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices.</p>
        </div>

        <div class="row">
            @foreach ($popularJobPostCategories as $category)
            @php
                $job_count = \App\Models\JobPost::where('category_id', $category->id)->where('status', 'Running')->count();
            @endphp
            <div class="col-lg-3 col-sm-6">
                <div class="category-item">
                    <i class="flaticon-wrench-and-screwdriver-in-cross"></i>
                    <h3>{{ $category->name }}</h3>
                    <p>{{ $job_count }} new Job</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
<!-- Job Category Section End -->

<!-- Jobs Section Start -->
<section class="job-section pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Jobs You May Be Interested In</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices.</p>
        </div>

        <div class="row">
            @foreach ($latestJobPosts as $jobPost)
            <div class="col-sm-6">
                <div class="job-card">
                    <div class="row align-items-center">
                        <div class="col-lg-3">
                            @if ($jobPost->thumbnail)
                            <div class="thumb-img">
                                <img src="{{ asset('uploads/job_thumbnail_photo') }}/{{ $jobPost->thumbnail }}" alt="Thumbnail">
                            </div>
                            @else
                            <div class="thumb-img">
                                <img src="{{ asset('frontend/img/job_post_default.jpg') }}" alt="Thumbnail">
                            </div>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="job-info">
                                <h3>
                                    {{ $jobPost->title }}
                                </h3>
                                <ul>
                                    <li>
                                        <i class='bx bx-briefcase' ></i>
                                        <strong>Category: {{ $jobPost->category->name }}</strong>
                                    </li>
                                    <li>
                                        <i class='bx bx-male-female'></i>
                                        <strong>Worker Needed: {{ $jobPost->need_worker }}</strong>
                                    </li>
                                    <li>
                                        <i class='bx bx-calendar' ></i>
                                        <strong>Approved At: {{ date('d M, Y', strtotime($jobPost->approved_at)) }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="job-save">
                                <span>
                                    <strong class="text-success">
                                        Worker Eern:
                                        <br>
                                        {{ get_site_settings('site_currency_symbol') }} {{ $jobPost->worker_charge }}
                                    </strong>
                                </span>
                                <span>
                                    <strong>
                                        Running:
                                        <br>
                                        {{ $jobPost->running_day }} Days
                                    </strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Jobs Section End -->

<!-- Companies Section Start -->
<section class="company-section pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Top Buyer</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
        </div>

        <div class="row">
            @foreach ($topBuyers as $buyer)
            <div class="col-lg-3 col-sm-6">
                <div class="company-card">
                    <div class="company-logo">
                        <img src="{{ asset('uploads/profile_photo') }}/{{ $buyer->user->profile_photo }}" alt="User Profile" style="width: 80px; height: 80px">
                    </div>
                    <div class="company-text">
                        <h3>{{ $buyer->user->name }}</h3>
                        <p>
                            <i class='bx bx-calendar'></i>
                            Join: {{ date('d M, Y', strtotime($buyer->user->created_at)) }}
                        </p>
                        <span class="company-btn">
                            0 Job Post
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- Companies Section End -->

<!-- Job Info Section Start -->
<div class="job-info-two pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="looking-job">
                    <div class="media">
                        <i class='flaticon-group align-self-start mr-3'></i>
                        <div class="media-body">
                            <h5 class="mt-0">Looking For a Job</h5>
                            <p>Your next role could be with one of these top leading organizations</p>

                            <a href="{{ route('login') }}">
                                Apply Now
                                <i class='bx bx-chevrons-right'></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="recruiting-card">
                    <div class="media">
                        <i class='flaticon-resume align-self-start mr-3'></i>
                        <div class="media-body">
                            <h5 class="mt-0">Are You Recruiting?</h5>
                            <p>Your next role could be with one of these top leading organizations</p>

                            <a href="{{ route('login') }}">
                                Apply Now
                                <i class='bx bx-chevrons-right'></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Job Info Section End -->

<!-- Counter Section Start -->
<div class="counter-section pt-100 pb-70">
    <div class="container">
        <div class="row counter-area">
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-resume"></i>
                    <h2><span>{{ $totalJobPosts }}</span></h2>
                    <p>Total Job Post</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-recruitment"></i>
                    <h2><span>{{ $runningJobPosts }}</span></h2>
                    <p>Running Job</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-employee"></i>
                    <h2><span>{{ $totalUser }}</span></h2>
                    <p>Total User</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-portfolio"></i>
                    <h2><span>{{ $totalWithdrawal }}</span></h2>
                    <p>Total Withdrawal</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Counter Section End -->

<!-- Testimonial Section Start -->
<section class="testimonial-section ptb-100">
    <div class="container">
        <div class="section-title text-center">
            <h2>What Client’s Say About Us</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus</p>
        </div>

        <div class="row">
            <div class="testimonial-slider owl-carousel owl-theme">
                @foreach ($testimonials as $testimonial)
                <div class="testimonial-items">
                    <div class="row align-items-center">
                        <div class="col-lg-5 col-md-6 offset-md-3 offset-lg-0 p-0">
                            <div class="testimonial-img">
                                <img src="{{ asset('uploads/testimonial_photo') }}/{{ $testimonial->photo }}" alt="testimonial image">
                            </div>
                            <div class="testimonial-img-text">
                                <h3>{{ $testimonial->name }}</h3>
                                <p>{{ $testimonial->designation }}</p>
                            </div>
                        </div>
                        <div class="col-lg-7 p-0">
                            <div class="testimonial-text">
                                <i class='flaticon-left-quotes-sign'></i>
                                <p>{{ $testimonial->comment }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!-- Testimonial Section End -->
@endsection
