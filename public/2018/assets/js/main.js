$(document).ready(function () {
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });
    const paymentMethods = $('.payment-methods');
    paymentMethods.find('input[type="radio"]').change(function(e) {
        const radio = $(this);
        paymentMethods.find('div.radio').removeClass('active');
        radio.parents('div.radio').addClass('active');
    });
});
