@extends('layouts.frontend')

@section('title', 'About Us')

@section('content')
<!-- About Section Start -->
<section class="about-section ptb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="about-text">
                    <div class="section-title">
                        <h2>How We Started</h2>
                    </div>

                    <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English.</p>

                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-img">
                    <img src="{{ asset('frontend') }}/img/about.jpg" alt="about image">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- About Section End -->

<!-- Way To Use Section Start -->
<section class="use-section pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Easiest Way To Use</h2>
        </div>

        <div class="row">
            <div class="col-lg-4 col-sm-6">
                <div class="use-text">
                    <span>1</span>
                    <i class='flaticon-website'></i>
                    <h3>Browse Job</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6">
                <div class="use-text">
                    <span>2</span>
                    <i class='flaticon-recruitment'></i>
                    <h3>Find Your Vaccancy</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 offset-sm-3 offset-lg-0">
                <div class="use-text">
                    <span>3</span>
                    <i class='flaticon-login'></i>
                    <h3>Submit Resume</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Way To Use Section End -->

<!-- Why Choose Section Start -->
<section class="why-choose-two pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Why You Choose Us Among Other Job Site?</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus</p>
        </div>

        <div class="row">
            <div class="col-lg-4 col-sm-6">
                <div class="choose-card">
                    <i class="flaticon-resume"></i>
                    <h3>Advertise Job</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore   </p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6">
                <div class="choose-card">
                    <i class="flaticon-recruitment"></i>
                    <h3>Recruiter Profiles</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore   </p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 offset-sm-3 offset-lg-0">
                <div class="choose-card">
                    <i class="flaticon-employee"></i>
                    <h3>Find Your Dream Job</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore   </p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Why Choose Section End -->

<!-- Grow Business Section Start -->
<div class="grow-business pb-100">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="grow-text">
                    <div class="section-title">
                        <h2>Grow Your Business Faster With Premium Advertising</h2>
                    </div>

                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis.Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy.
                    </p>

                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Consectetur adipiscing elit.
                    </p>

                    <div class="theme-btn">
                        <a href="#" class="default-btn">Checkout More</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="grow-img">
                    <img src="{{ asset('frontend') }}/img/grow-img.jpg" alt="grow image">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Grow Business Section End -->

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
<div class="testimonial-style-two ptb-100">
    <div class="container">
        <div class="section-title text-center">
            <h2>What Client’s Say About Us</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
        </div>

        <div class="row">
            <div class="testimonial-slider-two owl-carousel owl-theme">
                <div class="testimonial-items">
                    <div class="testimonial-text">
                        <i class='flaticon-left-quotes-sign'></i>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do mod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's.</p>
                    </div>

                    <div class="testimonial-info-text">
                        <h3>Alisa Meair</h3>
                        <p>CEO of  Company</p>
                    </div>
                </div>

                <div class="testimonial-items">
                    <div class="testimonial-text">
                        <i class='flaticon-left-quotes-sign'></i>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do mod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's.</p>
                    </div>

                    <div class="testimonial-info-text">
                        <h3>Adam Smith</h3>
                        <p>Web Developer</p>
                    </div>
                </div>

                <div class="testimonial-items">
                    <div class="testimonial-text">
                        <i class='flaticon-left-quotes-sign'></i>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do mod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis. Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's.</p>
                    </div>

                    <div class="testimonial-info-text">
                        <h3>John Doe</h3>
                        <p>Graphics Designer</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Testimonial Section End -->
@endsection


