@extends('layouts.frontend')

@section('title', 'FAQ')

@section('content')
<!-- Faq Section Start -->
<section class="faq-section pt-100 pb-70">
    <div class="container">
        <div class="section-title text-center">
            <h2>Frequently Asked Questions</h2>
        </div>
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="accordions">
                    @forelse ($faqs as $faq)
                    <div class="accordion-item">
                        <div class="accordion-title" data-tab="item{{ $faq->id }}">
                            <h2>{{ $faq->question }}<i class='bx bx-chevrons-right down-arrow'></i></h2>
                        </div>
                        <div class="accordion-content" id="item{{ $faq->id }}">
                            <p>{{ $faq->answer }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="accordion-item">
                        <div class="accordion-title" data-tab="item1">
                            <h2>No FAQ Found<i class='bx bx-chevrons-right down-arrow'></i></h2>
                        </div>
                        <div class="accordion-content" id="item1">
                            <p>No FAQ Found</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Faq Section End -->
@endsection
