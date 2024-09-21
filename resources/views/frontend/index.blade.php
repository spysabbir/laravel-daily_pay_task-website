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
                <img src="{{ asset('frontend') }}/img/choose.png" alt="why choose image">
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
            @foreach ($latestJobPosts as $item)
            <div class="col-sm-6">
                <div class="job-card">
                    <div class="row align-items-center">
                        <div class="col-lg-3">
                            <div class="thumb-img">
                                <a href="job-details.html">
                                    <img src="{{ asset('frontend') }}/img/company-logo/1.png" alt="company logo">
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="job-info">
                                <h3>
                                    <a href="job-details.html">Post-Room Operate</a>
                                </h3>
                                <ul>
                                    <li>Via <a href="#">Tourt Design LTD</a></li>
                                    <li>
                                        <i class='bx bx-location-plus'></i>
                                        Wellesley Rd, London
                                    </li>
                                    <li>
                                        <i class='bx bx-filter-alt' ></i>
                                        Accountancy
                                    </li>
                                    <li>
                                        <i class='bx bx-briefcase' ></i>
                                        Freelance
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="job-save">
                                <span>Full Time</span>
                                <a href="#">
                                    <i class='bx bx-heart'></i>
                                </a>
                                <p>
                                    <i class='bx bx-stopwatch' ></i>
                                    1 Hr Ago
                                </p>
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
            <h2>Top Companies</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
        </div>

        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="company-card">
                    <div class="company-logo">
                        <a href="job-grid.html">
                            <img src="{{ asset('frontend') }}/img/top-company/1.png" alt="company logo">
                        </a>
                    </div>
                    <div class="company-text">
                        <h3>Trophy  & Sans</h3>
                        <p>
                            <i class='bx bx-location-plus'></i>
                            Green Lanes, London
                        </p>
                        <a href="job-grid.html" class="company-btn">
                            25 Open Position
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="company-card">
                    <div class="company-logo">
                        <a href="job-grid.html">
                            <img src="{{ asset('frontend') }}/img/top-company/2.png" alt="company logo">
                        </a>
                    </div>
                    <div class="company-text">
                        <h3>Trout Design</h3>
                        <p>
                            <i class='bx bx-location-plus'></i>
                            Park Avenue, Mumbai
                        </p>
                        <a href="job-grid.html" class="company-btn">
                            35 Open Position
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="company-card">
                    <div class="company-logo">
                        <a href="job-grid.html">
                            <img src="{{ asset('frontend') }}/img/top-company/3.png" alt="company logo">
                        </a>
                    </div>
                    <div class="company-text">
                        <h3>Resland LTD</h3>
                        <p>
                            <i class='bx bx-location-plus'></i>
                            Betas Quence, London
                        </p>
                        <a href="job-grid.html" class="company-btn">
                            20 Open Position
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6">
                <div class="company-card">
                <div class="company-logo">
                    <a href="job-grid.html">
                        <img src="{{ asset('frontend') }}/img/top-company/4.png" alt="company logo">
                    </a>
                </div>
                <div class="company-text">
                    <h3>Lawn Hopper</h3>
                    <p>
                    <i class='bx bx-location-plus'></i>
                    Wellesley Rd, London
                    </p>
                    <a href="job-grid.html" class="company-btn">
                    45 Open Position
                    </a>
                </div>
                </div>
            </div>
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

                            <a href="job-list.html">
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

                            <a href="post-job.html">
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
                    <h2><span>1225</span></h2>
                    <p>Job Posted</p>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-recruitment"></i>
                    <h2><span>145</span></h2>
                    <p>Job Filed</p>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-portfolio"></i>
                    <h2><span>170</span></h2>
                    <p>Company</p>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-employee"></i>
                    <h2><span>125</span></h2>
                    <p>Members</p>
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
                <div class="testimonial-items">
                    <div class="row align-items-center">
                        <div class="col-lg-5 col-md-6 offset-md-3 offset-lg-0 p-0">
                            <div class="testimonial-img">
                                <img src="{{ asset('frontend') }}/img/testimonial-img.jpg" alt="testimonial image">
                            </div>
                            <div class="testimonial-img-text">
                                <h3>Alisa Meair</h3>
                                <p>CEO of  Company</p>
                            </div>
                        </div>
                        <div class="col-lg-7 p-0">
                            <div class="testimonial-text">
                                <i class='flaticon-left-quotes-sign'></i>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do mod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="testimonial-items">
                    <div class="row align-items-center">
                        <div class="col-lg-5 col-md-6 offset-md-3 offset-lg-0 p-0">
                            <div class="testimonial-img">
                                <img src="{{ asset('frontend') }}/img/testimonial-img-2.jpg" alt="testimonial image">
                            </div>
                            <div class="testimonial-img-text">
                                <h3>John Doe</h3>
                                <p>Web Designer</p>
                            </div>
                        </div>
                        <div class="col-lg-7 p-0">
                            <div class="testimonial-text">
                                <i class='flaticon-left-quotes-sign'></i>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do mod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Testimonial Section End -->
@endsection
