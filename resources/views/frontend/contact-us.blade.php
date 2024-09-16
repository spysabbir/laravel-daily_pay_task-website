@extends('layouts.frontend')

@section('title', 'Contact Us')

@section('content')
<!-- Contact Section Start -->
<div class="contact-card-section ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="contact-card">
                            <i class='bx bx-phone-call'></i>
                            <ul>
                                <li>
                                    <a href="tel:{{ get_site_settings('site_support_phone') }}">
                                        {{ get_site_settings('site_support_phone') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <div class="contact-card">
                            <i class='bx bx-mail-send' ></i>
                            <ul>
                                <li>
                                    <a href="mailto:{{ get_site_settings('site_support_email') }}">
                                        {{ get_site_settings('site_support_email') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 offset-sm-3 offset-md-0">
                        <div class="contact-card">
                            <i class='bx bx-location-plus' ></i>
                            <ul>
                                <li>
                                    {{ get_site_settings('site_address') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Contact Section End -->

<!-- Contact Form Start -->
<section class="contact-form-section pb-100">
    <div class="container">
        <div class="contact-area">
            <h3>Lets' Talk With Us</h3>
            <form id="contactForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="name" id="name" class="form-control" required data-error="Please enter your name" placeholder="Your Name">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="email" name="email" id="email" class="form-control" required data-error="Please enter your email" placeholder="Your Email">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="number" name="number" id="number" class="form-control" required data-error="Please enter your number" placeholder="Phone Number">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="subject" id="subject" class="form-control" required data-error="Please enter your subject" placeholder="Your Subject">
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <textarea name="message" class="form-control message-field" id="message" cols="30" rows="7" required data-error="Please type your message" placeholder="Write Message"></textarea>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 text-center">
                        <button type="submit" class="default-btn contact-btn">
                            Send Message
                        </button>
                        <div id="msgSubmit" class="h3 alert-text text-center hidden"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- Contact Form End -->
@endsection

