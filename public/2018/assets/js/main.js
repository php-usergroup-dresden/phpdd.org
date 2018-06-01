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
    const termsOptIn = $('#termsOptIn');
    const buttonPurchase = $('#buttonPurchase');
    const stripeButton = $('button.stripe-button-el');

    function checkOptIns() {
        console.log('checking');
        let cocChecked = cocOptIn.prop('checked');
        let termsChecked = termsOptIn.prop('checked');

        console.log(cocChecked);
        console.log(termsChecked);

        buttonPurchase.prop('disabled', !(cocChecked && termsChecked));
        stripeButton.prop('disabled', !(cocChecked && termsChecked));
    }
    cocOptIn.change(function () {
        checkOptIns();
    });
    termsOptIn.change(function () {
        checkOptIns();
    });
    checkOptIns();
});
