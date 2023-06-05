<!-- Modal -->
<div id="news-modal"
     class="modal fade"
     tabindex="-1"
     role="dialog"
     aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel">{{ __('frontend/user.news.title') }}</h4>
                <button type="button" class="close btn-close-icon" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="newsAccordion" class="mb-10 accordion-with-icon">
                    <div class="container">
                        <div class="row justify-content-center">
                            @foreach($news as $article)
                            <div class="col-md-12 mb-3">
                                <div class="card bg-shahzad-lightgray">
                                    <div class="card-header header-colour-none" id="newsHeading-{{ $loop->iteration }}">
                                        <span class="pt-3"
                                              data-toggle="collapse"
                                              data-target="#newsCollapse-{{ $loop->iteration }}"
                                              {{-- aria-expanded="@if($loop->iteration == 1) true @else false @endif" --}}
                                              aria-expanded="true"
                                              aria-controls="newsCollapse-{{ $loop->iteration }}">
                                            <div class="row pt-3">
                                                <div class="col-md-12">
                                                    <span class="letter-spacing-shahzad theme-colour-red">{{ $article->title }}</span>
                                                </div>
                                            </div>
                                        </span>
                                    </div>
                
                                    <div id="newsCollapse-{{ $loop->iteration }}" class="collapse show" aria-labelledby="newsHeading-{{ $loop->iteration }}">
                                        <div class="card-body bg-light-gray">
                                            {!! $article->body !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($news) && $news->count() > 0)
<script type="text/javascript">
    $(document).ready(function () {
        var lastOpenedTime = window.localStorage.getItem('news-last-opened-time');
        if (typeof lastOpenedTime === "undefined" || lastOpenedTime === null) {
            window.localStorage.setItem('news-last-opened-time', new Date());
        }
        
        if (typeof lastOpenedTime !== "undefined" && lastOpenedTime !== null) {
            try {
                var lastOpenedDateTime = new Date(lastOpenedTime);
                var now = new Date();

                if (lastOpenedDateTime instanceof Date) {
                    lastOpenedDateTime.setMinutes(lastOpenedDateTime.getMinutes() + {{ env('NEWS_POPUP_HIDE_MINUETS', 240) }});

                    if (lastOpenedDateTime.getTime() < now.getTime()) {
                        window.localStorage.setItem('news-last-opened-time', new Date());

                        $('#news-modal').modal('show');
                    }
                }
            } catch (e) {}
        } else {
            window.localStorage.setItem('news-last-opened-time', new Date());

            $('#news-modal').modal('show');
        }
    });
</script>
@endif
