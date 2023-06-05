@extends('frontend.layouts.app')

@section('content')
{{--
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3 class="page-title">{{ __('frontend/main.faq') }}</h3>
        </div>
    </div>
</div>
--}}
@foreach($faqCategories as $faqCategory)
<div class="container">
    <div class="row justify-content-center mb-3 mt-3">
        <div class="col-md-8">
            <h5 class="theme-colour-red">{{ $faqCategory->name }}</h5>
        </div>
    </div>
</div>

<div id="faqAccordion-{{ $loop->iteration }}" class="mb-15 accordion-with-icon">
    <div class="container">
        <div class="row justify-content-center">
            @foreach($faqCategory->getEntries() as $faq)
            <div class="col-md-8 mb-15">
                <div class="card bg-shahzad-lightgray">
                    <div class="card-header header-colour-none" id="faqHeading-{{ $loop->parent->iteration }}-{{ $loop->iteration }}">
                        <span class="pt-3 collapsed"
                              data-toggle="collapse"
                              data-target="#faqCollapse-{{ $loop->parent->iteration }}-{{ $loop->iteration }}"
                              {{-- aria-expanded="@if($loop->iteration == 1) true @else false @endif" --}}
                              aria-expanded="false"
                              aria-controls="faqCollapse-{{ $loop->parent->iteration }}-{{ $loop->iteration }}">
                            <div class="row pt-3">
                                <div class="col-md-12">
                                    <span class="letter-spacing-shahzad">{{ $faq->question }}</span>
                                </div>
                            </div>
                        </span>
                    </div>

                    <div id="faqCollapse-{{ $loop->parent->iteration }}-{{ $loop->iteration }}" class="collapse {{-- @if($loop->iteration == 1) show @endif --}}" aria-labelledby="faqHeading-{{ $loop->parent->iteration }}-{{ $loop->iteration }}" data-parent="#faqAccordion-{{ $loop->parent->iteration }}">
                        <div class="card-body bg-light-gray">
                            @php
                                try {
                                    $answer = strlen($faq->answer) > 0 ? nl2br($faq->answer) : '';
                                } catch (\Exception $e) {
                                    $answer = '';
                                }
                            @endphp
                            {!! $answer !!}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach
            
@endsection
