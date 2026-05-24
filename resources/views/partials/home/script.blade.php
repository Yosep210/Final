<script src="{{ asset('assets/js/jquery-3.4.1.min.js.download') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js.download') }}"></script>
<script src="{{ asset('assets/js/wow.min.js.download') }}"></script>
<script src="{{ asset('assets/js/easing.min.js.download') }}"></script>
<script src="{{ asset('assets/js/waypoints.min.js.download') }}"></script>
<script src="{{ asset('assets/js/counterup.min.js.download') }}"></script>
<script src="{{ asset('assets/js/owl.carousel.min.js.download') }}"></script>
<script src="{{ asset('assets/js/isotope.pkgd.min.js.download') }}"></script>
<script src="{{ asset('assets/js/lightbox.min.js.download') }}"></script>
<script src="{{ asset('assets/js/main.js.download') }}"></script>

<div id="lightboxOverlay" class="lightboxOverlay" style="display: none;"></div>
<div id="lightbox" class="lightbox" style="display: none;">
    <div class="lb-outerContainer">
        <div class="lb-container"><img class="lb-image"
                src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==">
            <div class="lb-nav"><a class="lb-prev" href="{{ route('home') }}"></a><a class=" lb-next"
                    href="{{ route('home') }}"></a></div>
            <div class=" lb-loader"><a class="lb-cancel"></a></div>
        </div>
    </div>
    <div class="lb-dataContainer">
        <div class="lb-data">
            <div class="lb-details"><span class="lb-caption"></span><span class="lb-number"></span></div>
            <div class="lb-closeContainer"><a class="lb-close"></a></div>
        </div>
    </div>
</div>