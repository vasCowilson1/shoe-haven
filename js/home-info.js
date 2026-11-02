(function () {
    'use strict';

    var phonePattern = /^(?:\+?256|0)7\d{8}$/;

    function validatePhone(phone) {
        phone.setCustomValidity(phonePattern.test(phone.value.replace(/[\s-]/g, '')) ? '' : 'Enter a valid Ugandan mobile number.');
    }

    function bindForm(form) {
        var status = document.getElementById(form.getAttribute('data-status'));
        var phone = form.elements.phone;

        if (!phone || !status) return;

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            validatePhone(phone);
            form.classList.add('was-validated');

            if (!form.checkValidity()) {
                status.textContent = 'Please check the highlighted fields.';
                status.className = 'small ms-3 text-danger';
                return;
            }

            status.textContent = 'Thanks — we will be in touch shortly.';
            status.className = 'small ms-3 text-success';
            form.reset();
            form.classList.remove('was-validated');
        });

        phone.addEventListener('input', function () {
            validatePhone(phone);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var homeForm = document.getElementById('homeContactForm');
        var contactForm = document.getElementById('contactPageForm');

        if (homeForm) bindForm(homeForm);
        if (contactForm) bindForm(contactForm);
    });
}());
