<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#14141a">
    <title>@yield('title', config('app.name', 'Shoe Haven'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @stack('styles')
<style>
.sh-product-card .sh-product-img img { transition: opacity .25s ease; }
</style>
</head>
<body>
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <div id="sh-toast" class="sh-toast" style="display: none;">
        <div class="sh-toast-icon" id="sh-toast-icon"><i class="bi bi-check-circle-fill"></i></div>
        <div class="sh-toast-body">
            <div class="sh-toast-message" id="sh-toast-message"></div>
            <div class="sh-toast-count" id="sh-toast-count"><span></span> item(s) in cart</div>
        </div>
    </div>

    <button id="sh-scroll-top" class="sh-scroll-top" aria-label="Scroll to top">
        <i class="bi bi-chevron-up"></i>
    </button>

    @if (session('cart_toast'))
        @php $t = session('cart_toast'); @endphp
        <script>
            (function() {
                var msg = @json($t['message']);
                var count = @json($t['count']);
                var toast = document.getElementById('sh-toast');
                toast.querySelector('.sh-toast-message').textContent = msg;
                toast.querySelector('.sh-toast-count span').textContent = count;
                toast.style.display = 'flex';
                toast.classList.add('sh-toast-show');
                setTimeout(function() {
                    toast.classList.remove('sh-toast-show');
                    toast.classList.add('sh-toast-hide');
                    setTimeout(function() { toast.style.display = 'none'; toast.classList.remove('sh-toast-hide'); }, 3000);
                }, 3000);
            })();
        </script>
    @elseif (session('status'))
        <script>
            (function() {
                var msg = @json(session('status'));
                var toast = document.getElementById('sh-toast');
                toast.querySelector('.sh-toast-message').textContent = msg;
                toast.querySelector('.sh-toast-count').style.display = 'none';
                toast.style.display = 'flex';
                toast.classList.add('sh-toast-show');
                setTimeout(function() {
                    toast.classList.remove('sh-toast-show');
                    toast.classList.add('sh-toast-hide');
                    setTimeout(function() { toast.style.display = 'none'; toast.classList.remove('sh-toast-hide'); }, 4000);
                }, 4000);
            })();
        </script>
    @endif

    <script>
        (function() {
            var btn = document.getElementById('sh-scroll-top');
            if (btn) {
                window.addEventListener('scroll', function() {
                    btn.classList.toggle('visible', window.scrollY > 400);
                });
                btn.addEventListener('click', function() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        })();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sh-product-img img[data-gallery]').forEach(function(img) {
                var urls = img.getAttribute('data-gallery').split(',');
                if (urls.length < 2) return;
                var timer, idx = 1;
                img.addEventListener('mouseenter', function() {
                    idx = 1;
                    timer = setInterval(function() {
                        img.style.opacity = '0';
                        setTimeout(function() {
                            img.src = urls[idx % urls.length];
                            img.style.opacity = '1';
                            idx++;
                        }, 250);
                    }, 800);
                });
                img.addEventListener('mouseleave', function() {
                    clearInterval(timer);
                    img.style.opacity = '0';
                    setTimeout(function() {
                        img.src = urls[0];
                        img.style.opacity = '1';
                    }, 250);
                    idx = 1;
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
