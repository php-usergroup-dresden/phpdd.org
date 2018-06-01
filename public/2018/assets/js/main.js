$(document).ready(function () {
    $('[data-toggle=offcanvas]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });
    const paymentMethods = $('.payment-methods');
    paymentMethods.find('input[type="radio"]').change(function (e) {
        const radio = $(this);
        paymentMethods.find('div.radio').removeClass('active');
        radio.parents('div.radio').addClass('active');
    });
    const cocOptIn = $('#cocOptIn');
    const buttonPurchase = $('#buttonPurchase');
    const stripeButton = $('button.stripe-button-el');
    cocOptIn.change(function () {
        const checked = $(this).prop('checked');
        buttonPurchase.prop('disabled', !checked);
        stripeButton.prop('disabled', !checked);
    });
    stripeButton.prop('disabled', true);
});
