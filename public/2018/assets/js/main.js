$(document).ready(function () {
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });

    var gallery = $('#image-gallery');
    if (gallery.length) {
        $.get('/images.php', function (result) {
            $.each(result,function(orig, img) {
                gallery.append(
                    '<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3"><a href="' + orig +'" target="_blank">' +
                    '<img src="' + img.url + '" class="img-responsive img-rounded">' +
                    '</a>' +
                    '</div>'
                );
            });
        });
    }
});
