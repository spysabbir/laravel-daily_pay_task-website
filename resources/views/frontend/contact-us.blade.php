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
            <div class="alert alert-success mb-3" style="display: none;" id="contactSuccessMessage">
                <strong>Success!</strong> Your message has been sent successfully.
            </div>
            <form id="contactForm" class="contact-form" action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Your Name">
                            <span class="text-danger error-text name_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Your Email">
                            <span class="text-danger error-text email_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="phone" class="form-control" placeholder="Your Phone">
                            <span class="text-danger error-text phone_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control" placeholder="Your Subject">
                            <span class="text-danger error-text subject_error"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <textarea name="message" class="form-control message-field" cols="30" rows="7" placeholder="Your Message"></textarea>
                            <span class="text-danger error-text message_error"></span>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 text-center">
                        <button type="submit" class="default-btn contact-btn">Send Message</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<!-- Contact Form End -->
@endsection

