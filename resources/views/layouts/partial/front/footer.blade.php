<footer class="p-md-5 p-3 ">
    <div class="row footer-content">
        <div class="col-12">
            <p class="text-center">{{ __('front.footer.app_description') }}</p>
        </div>
        <div class="col-12 text-center">
            <a href="{{ config('feed.feeds.main.url') }}" id="rss-link"
               class="text-decoration-none bg-light px-1"
               target="_blank" title="{{ __('custom.subscribe') }}">
                <i class="fas fa-rss-square mr-2"></i> RSS Feed
            </a>
        </div>
    </div>

    <div class="row footer-content">
        <div class="col-md-2 col-6">
            <img src="{{ asset('img/eu_white.png') }}" alt="EU" class="img-fluid"/>
        </div>
        <div class="col-md-8 d-md-block d-none"></div>
        <div class="col-md-2 col-6">
            <img src="{{ asset('img/op_white.png') }}" alt="OP" class="img-fluid"/>
        </div>
    </div>
</footer>
