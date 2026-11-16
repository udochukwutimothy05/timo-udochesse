/* =====================================================
   UDOCHESSE FOOD NETWORK - MAIN JAVASCRIPT
   ===================================================== */
document.addEventListener('DOMContentLoaded', function () {

    /* ---------- Navbar shadow on scroll ---------- */
    const nav = document.querySelector('.main-nav');
    /* ---------- Back to top button ---------- */
    const backBtn = document.getElementById('backToTop');

    function onScroll() {
        const y = window.scrollY;
        if (nav) nav.classList.toggle('scrolled', y > 20);
        if (backBtn) backBtn.classList.toggle('show', y > 400);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    if (backBtn) {
        backBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ---------- Reveal elements as they scroll into view ---------- */
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        revealEls.forEach(function (el) { observer.observe(el); });
    } else {
        revealEls.forEach(function (el) { el.classList.add('visible'); });
    }

    /* ---------- Auto-hide alerts after 6 seconds ---------- */
    document.querySelectorAll('.alert-dismissible').forEach(function (alertEl) {
        setTimeout(function () {
            if (window.bootstrap) {
                bootstrap.Alert.getOrCreateInstance(alertEl).close();
            }
        }, 6000);
    });

    /* ---------- Bootstrap form validation (add class "needs-validation" to a form) ---------- */
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    /* ---------- Date inputs: block past dates (add attribute data-min-today) ---------- */
    document.querySelectorAll('input[data-min-today]').forEach(function (input) {
        const now = new Date();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        input.min = now.getFullYear() + '-' + month + '-' + day;
    });

    /* ---------- Phone inputs: allow only digits, +, spaces and dashes ---------- */
    document.querySelectorAll('input[data-phone]').forEach(function (input) {
        input.addEventListener('input', function () {
            input.value = input.value.replace(/[^\d+\s-]/g, '');
        });
    });

    /* ---------- Gallery filter (buttons with data-filter, items with data-category) ---------- */
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryCols = document.querySelectorAll('[data-category]');
    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');
            const filter = btn.dataset.filter;
            galleryCols.forEach(function (col) {
                col.classList.toggle('d-none', filter !== 'all' && col.dataset.category !== filter);
            });
        });
    });

    /* ---------- Gallery lightbox (items with data-img open modal #lightboxModal) ---------- */
    const lightbox = document.getElementById('lightboxModal');
    if (lightbox && window.bootstrap) {
        const modal = new bootstrap.Modal(lightbox);
        const lbImg = lightbox.querySelector('img');
        const lbCaption = lightbox.querySelector('.lb-caption');
        document.querySelectorAll('.gallery-item[data-img]').forEach(function (item) {
            item.addEventListener('click', function () {
                lbImg.src = item.dataset.img;
                lbImg.alt = item.dataset.title || '';
                if (lbCaption) lbCaption.textContent = item.dataset.title || '';
                modal.show();
            });
        });
    }

    /* ---------- Prevent double submits (add attribute data-loading to submit buttons) ---------- */
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            if (!form.checkValidity()) return;
            const btn = form.querySelector('button[data-loading]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
            }
        });
    });
});