@extends('layouts.frontend')

@section('title', 'Home')

@section('content')
<!-- Banner Section Start -->
<div class="banner-style-two">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="container">
                <div class="banner-text">
                    <span>Wellcome to {{ get_site_settings('site_name') }}</span>
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
                        <h2>Why You Choose {{ get_site_settings('site_name') }}?</h2>
                        <p>Daily Micro Tasks provides the flexibility to earn on your own schedule. Complete simple tasks for quick payouts, and enjoy a supportive community. Turn your spare time into cash and explore diverse opportunities to boost your income effortlessly!</p>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="media">
                                <i class="flaticon-group align-self-top mr-3"></i>
                                <div class="media-body">
                                    <h5 class="mt-0">Flexibility</h5>
                                    <p>Work on your own schedule and complete tasks whenever it’s convenient for you.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="media">
                                <i class="flaticon-research align-self-top mr-3"></i>
                                <div class="media-body">
                                    <h5 class="mt-0">Simple Tasks</h5>
                                    <p>Enjoy a variety of easy-to-complete tasks that require minimal time and effort.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="media">
                                <i class="flaticon-discussion align-self-top mr-3"></i>
                                <div class="media-body">
                                    <h5 class="mt-0">Quick Earnings</h5>
                                    <p>Start earning money immediately with fast payouts for completed tasks.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="media">
                                <i class="flaticon-recruitment align-self-top mr-3"></i>
                                <div class="media-body">
                                    <h5 class="mt-0">Community Support</h5>
                                    <p>Join a vibrant community of earners and share tips to maximize your profits.</p>
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

<!-- Task Category Section Start -->
<div class="category-style-two pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Popular Tasks Category</h2>
            <p>Discover our Popular Tasks Category at Daily Micro Tasks, featuring in-demand tasks like online surveys, data entry, and content moderation. These easy, rewarding opportunities cater to various skills and interests, making it simple to earn extra cash on your schedule!</p>
        </div>

        <div class="row">
            @forelse ($topCategories as $category)
            <div class="col-lg-3 col-sm-6">
                <div class="category-item">
                    <i class="flaticon-wrench-and-screwdriver-in-cross"></i>
                    <h3>{{ $category->category->name }}</h3>
                    <p>{{ $category->count }} new Task</p>
                </div>
            </div>
            @empty
            <div class="col-lg-12">
                <div class="category-item">
                    <h3 class="text-center">No Category Found</h3>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>
<!-- Task Category Section End -->

<!-- Jobs Section Start -->
<section class="job-section pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Tasks You May Be Interested In</h2>
            <p>Explore our curated list of tasks tailored to your skills and preferences. From online surveys to data entry, discover opportunities that match your interests. Find rewarding tasks that fit your schedule and start earning extra income today!</p>
        </div>

        <div class="row">
            @forelse ($latestPostTasks as $postTask)
            <div class="col-sm-6">
                <div class="job-card">
                    <div class="row align-items-center">
                        <div class="col-lg-3">
                            @if ($postTask->thumbnail)
                            <div class="thumb-img">
                                <img src="{{ asset('uploads/task_thumbnail_photo') }}/{{ $postTask->thumbnail }}" alt="Thumbnail">
                            </div>
                            @else
                            <div class="thumb-img">
                                <img src="{{ asset('frontend/img/task_post_default.jpg') }}" alt="Thumbnail">
                            </div>
                            @endif
                        </div>

                        <div class="col-lg-6">
                            <div class="job-info">
                                <h3>
                                    {{ $postTask->title }}
                                </h3>
                                <ul>
                                    <li>
                                        <i class='bx bx-briefcase' ></i>
                                        <strong>Category: {{ $postTask->category->name }}</strong>
                                    </li>
                                    <li>
                                        <i class='bx bx-male-female'></i>
                                        <strong>Worker Needed: {{ $postTask->worker_needed }}</strong>
                                    </li>
                                    <li>
                                        <i class='bx bx-calendar' ></i>
                                        <strong>Approved At: {{ date('d M, y h:i A', strtotime($postTask->created_at)) }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-3">
                            <div class="job-save">
                                <span>
                                    <strong class="text-success">
                                        Income Of Each Worker:
                                        <br>
                                        {{ get_site_settings('site_currency_symbol') }} {{ $postTask->income_of_each_worker }}
                                    </strong>
                                </span>
                                <span>
                                    <strong>
                                        Work Duration:
                                        <br>
                                        {{ $postTask->work_duration }} Days
                                    </strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-lg-12">
                <div class="job-card">
                    <h3 class="text-center">No Task Found</h3>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>
<!-- Jobs Section End -->

<!-- Companies Section Start -->
<section class="company-section pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Top Buyer</h2>
            <p>Meet our Top Buyer section, showcasing the most sought-after tasks on Daily Micro Tasks. These high-demand tasks offer excellent rewards and quick payouts. Join today to capitalize on popular opportunities and maximize your earnings with the best tasks available!</p>
        </div>

        <div class="row">
            @forelse ($topBuyers as $buyer)
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
                            {{ $buyer->count }} Post Task
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-lg-12">
                <div class="company-card">
                    <h3 class="text-center">No Buyer Found</h3>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>
<!-- Companies Section End -->

<!-- Task Info Section Start -->
<div class="job-info-two pt-100 pb-70">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="looking-job">
                    <div class="media">
                        <i class='flaticon-group align-self-start mr-3'></i>
                        <div class="media-body">
                            <h5 class="mt-0">Looking For a Task</h5>
                            <p>Browse our diverse selection of tasks tailored to your skills. Start earning extra income easily and conveniently today!</p>

                            <a href="{{ route('find_tasks.clear.filters') }}">
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
                            <h5 class="mt-0">Posting a Task</h5>
                            <p>Post your task and connect with skilled workers eager to complete your task. Get quality results and quick turnaround times!</p>

                            <a href="{{ route('post_task') }}">
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
<!-- Task Info Section End -->

<!-- Counter Section Start -->
<div class="counter-section pt-100 pb-70">
    <div class="container">
        <div class="row counter-area">
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-resume"></i>
                    <h2><span>{{ $totalPostTask }}</span></h2>
                    <p>Total Posted Task</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="counter-text">
                    <i class="flaticon-recruitment"></i>
                    <h2><span>{{ $runningPostTasks }}</span></h2>
                    <p>Running Task</p>
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
            <p>Our clients love Daily Micro Tasks for its user-friendly interface and diverse task options. They appreciate the quick payouts and the flexibility to earn on their own schedules. Join our community and see why they keep coming back!</p>
        </div>

        <div class="row">
            <div class="testimonial-slider owl-carousel owl-theme">
                @forelse ($testimonials as $testimonial)
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
                @empty
                <div class="testimonial-items">
                    <h3 class="text-center">No Testimonial Found</h3>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
<!-- Testimonial Section End -->
@endsection
