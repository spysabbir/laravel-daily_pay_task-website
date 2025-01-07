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

                    <p>Daily Micro Tasks was founded to provide individuals with flexible earning opportunities in a fast-paced digital world. Inspired by the gig economy, we aimed to create a user-friendly platform where anyone can complete simple tasks and earn extra income. Our mission is to empower users to maximize their potential!</p>

                    <p>Daily Micro Tasks was created to empower individuals to earn money easily by completing simple tasks in their spare time.</p>
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
                    <i class='flaticon-login'></i>
                    <h3>Simple Registration</h3>
                    <p>Quickly create your account with an easy sign-up process to start exploring tasks.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6">
                <div class="use-text">
                    <span>2</span>
                    <i class='flaticon-consultation'></i>
                    <h3>Intuitive Navigation</h3>
                    <p>Effortlessly browse and filter tasks by category or popularity to find what suits you best.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 offset-sm-3 offset-lg-0">
                <div class="use-text">
                    <span>3</span>
                    <i class='flaticon-computer'></i>
                    <h3>Fast Completion and Payout</h3>
                    <p>Complete tasks at your own pace and enjoy prompt payouts for your efforts.</p>
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
            <h2>Why You Choose Us Among Other Task Site?</h2>
            <p>Daily Micro Tasks stands out with its user-friendly interface, diverse task offerings, and quick payouts. Enjoy flexible earning opportunities that fit your schedule, along with a supportive community that helps you maximize your income effortlessly. Join us today!</p>
        </div>

        <div class="row">
            <div class="col-lg-4 col-sm-6">
                <div class="choose-card">
                    <i class="flaticon-discussion"></i>
                    <h3>User-Friendly Experience</h3>
                    <p>Our platform is designed for simplicity, making it easy for users to find and complete tasks quickly. A seamless interface ensures you spend less time navigating and more time earning.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6">
                <div class="choose-card">
                    <i class="flaticon-website"></i>
                    <h3>Diverse Opportunities</h3>
                    <p>We offer a wide range of tasks tailored to different skills and interests, from surveys and data entry to content moderation. This variety keeps your earning potential high and your experience engaging.</p>
                </div>
            </div>

            <div class="col-lg-4 col-sm-6 offset-sm-3 offset-lg-0">
                <div class="choose-card">
                    <i class="flaticon-computer"></i>
                    <h3>Quick Payouts</h3>
                    <p>At Daily Micro Tasks, we prioritize timely payments. Enjoy hassle-free earnings with fast payout options, ensuring you receive your rewards as soon as possible, making your efforts truly worthwhile.</p>
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

                    <p>Unlock your brand’s potential with premium advertising on Daily Micro Tasks. Reach a targeted audience eager to engage with your services. Our platform offers flexible ad placements, allowing you to showcase your offerings effectively. Boost visibility, drive traffic, and accelerate growth with our tailored advertising solutions today!</p>

                    <p>Accelerate your business growth with premium advertising on Daily Micro Tasks. Connect with a targeted audience and showcase your services effectively. Enjoy flexible ad placements that enhance visibility and drive traffic, helping you achieve your marketing goals quickly and efficiently!</p>

                    <div class="theme-btn">
                        <a href="{{ route('contact.us') }}" class="default-btn">Contact Us</a>
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
                    <i class="flaticon-results"></i>
                    <h2><span>{{ $totalPostTask }}</span></h2>
                    <p>Total Post Task</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-research"></i>
                    <h2><span>{{ $runningPostTask }}</span></h2>
                    <p>Running Task</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-group"></i>
                    <h2><span>{{ $totalUser }}</span></h2>
                    <p>Total User</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-accounting"></i>
                    <h2><span>{{ $totalWithdraw }}</span></h2>
                    <p>Total Withdraw</p>
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
            <p>Clients love Daily Micro Tasks for its user-friendly design and diverse task selection. They highlight the quick payouts and flexible earning opportunities, enabling them to maximize their income effortlessly. Our supportive community keeps them engaged and motivated to succeed!</p>
        </div>

        <div class="row">
            <div class="testimonial-slider-two owl-carousel owl-theme">
                @forelse ($testimonials as $testimonial)
                <div class="testimonial-items">
                    <div class="testimonial-text">
                        <i class='flaticon-left-quotes-sign'></i>
                        <p>{{ $testimonial->comment }}</p>
                    </div>
                    <div class="testimonial-info-text">
                        <h3>{{ $testimonial->name }}</h3>
                        <p>{{ $testimonial->designation }}</p>
                    </div>
                </div>
                @empty
                <div class="testimonial-items">
                    <h3 class="text-center">No Testimonial Found</h3>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
<!-- Testimonial Section End -->
@endsection


