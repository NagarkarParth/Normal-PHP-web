(function () {
    'use strict';

    var root = document.documentElement;

    function setTheme(theme) {
        root.setAttribute('data-theme', theme);
        root.setAttribute('data-bs-theme', theme);
        localStorage.setItem('ybt-theme', theme);
    }

    document.querySelectorAll('.theme-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        });
    });

    // Flash messages fade out on their own but stay dismissable.
    document.querySelectorAll('.flash-toast').forEach(function (toast) {
        var close = function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-8px)';
            setTimeout(function () { toast.remove(); }, 220);
        };
        toast.querySelector('.flash-close').addEventListener('click', close);
        setTimeout(close, 6000);
    });

    var sidebar = document.getElementById('adminSidebar');
    var backdrop = document.getElementById('adminBackdrop');
    var toggle = document.getElementById('adminSidebarToggle');
    if (sidebar && toggle && backdrop) {
        var closeSidebar = function () {
            sidebar.classList.remove('open');
            backdrop.classList.remove('show');
        };
        toggle.addEventListener('click', function () {
            sidebar.classList.add('open');
            backdrop.classList.add('show');
        });
        backdrop.addEventListener('click', closeSidebar);
    }

    // Product gallery
    var galleryMain = document.getElementById('galleryMain');
    if (galleryMain) {
        document.querySelectorAll('.gallery-thumbs img').forEach(function (thumb) {
            thumb.addEventListener('click', function () {
                galleryMain.src = thumb.dataset.full || thumb.src;
                document.querySelectorAll('.gallery-thumbs img').forEach(function (t) { t.classList.remove('active'); });
                thumb.classList.add('active');
            });
        });
    }

    // Keep the filter form in sync when a select changes on mobile.
    document.querySelectorAll('[data-autosubmit]').forEach(function (element) {
        element.addEventListener('change', function () { element.form.submit(); });
    });

    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    // Razorpay handoff: the checkout page renders the options as JSON.
    var payload = document.getElementById('razorpayCheckout');
    if (payload && window.Razorpay) {
        var options = JSON.parse(payload.textContent);
        var form = document.getElementById('razorpayConfirmForm');
        options.handler = function (response) {
            form.querySelector('[name="razorpay_payment_id"]').value = response.razorpay_payment_id;
            form.querySelector('[name="razorpay_order_id"]').value = response.razorpay_order_id;
            form.querySelector('[name="razorpay_signature"]').value = response.razorpay_signature;
            form.submit();
        };
        options.modal = {
            ondismiss: function () {
                window.location.href = payload.dataset.cancelUrl;
            }
        };
        new window.Razorpay(options).open();
    }
}());
