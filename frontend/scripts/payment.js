document.addEventListener("DOMContentLoaded", function() {
    const paymentMethodRadios = document.querySelectorAll('input[name="payment-method"]');
    const cardContainer = document.querySelector('.cardcontainer');

    cardContainer.style.display = 'none';

    function toggleCardContainer() {
        const selectedMethod = document.querySelector('input[name="payment-method"]:checked').value;
        if (selectedMethod === "creditcard") {
            cardContainer.style.display = 'block';
        } else {
            cardContainer.style.display = 'none';
        }
    }

    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', toggleCardContainer);
    });

    toggleCardContainer();
});
