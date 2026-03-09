<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="theme-color" content="#F0404B">
    <title inertia>{{ config('app.name', 'HEALTIVA') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
    <style>
        /* â”€â”€ Splash Screen â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
        #splash {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: linear-gradient(135deg, #F0404B 0%, #c72d37 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 32px;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        #splash.hide {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Logo */
        #splash-logo {
            height: 80px;
            width: auto;
            animation: splashLogoIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            opacity: 0;
        }
        @keyframes splashLogoIn {
            0%   { opacity: 0; transform: scale(0.7) translateY(16px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Ring spinner */
        #splash-spinner {
            width: 44px;
            height: 44px;
            animation: splashFadeIn 0.4s ease 0.5s forwards;
            opacity: 0;
        }
        @keyframes splashFadeIn {
            to { opacity: 1; }
        }
        #splash-spinner circle {
            fill: none;
            stroke: rgba(255,255,255,0.25);
            stroke-width: 3;
        }
        #splash-spinner .track {
            stroke: white;
            stroke-dasharray: 80;
            stroke-dashoffset: 80;
            stroke-linecap: round;
            animation: spinnerDraw 0.8s ease 0.6s forwards, spinnerRotate 1.2s linear 1.4s infinite;
            transform-origin: center;
        }
        @keyframes spinnerDraw {
            to { stroke-dashoffset: 20; }
        }
        @keyframes spinnerRotate {
            to { transform: rotate(360deg); }
        }

        /* Progress bar */
        #splash-bar-wrap {
            width: 140px;
            height: 3px;
            background: rgba(255,255,255,0.2);
            border-radius: 99px;
            overflow: hidden;
            animation: splashFadeIn 0.4s ease 0.5s forwards;
            opacity: 0;
        }
        #splash-bar {
            height: 100%;
            width: 0%;
            background: white;
            border-radius: 99px;
            animation: barProgress 1.4s cubic-bezier(0.4, 0, 0.2, 1) 0.7s forwards;
        }
        @keyframes barProgress {
            0%   { width: 0%; }
            60%  { width: 75%; }
            100% { width: 100%; }
        }

        /* Dot grid bg on splash */
        #splash::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255,255,255,0.08) 1.5px, transparent 1.5px);
            background-size: 28px 28px;
            pointer-events: none;
        }
    </style>
</head>
<body class="font-poppins antialiased bg-app">

    <!-- â”€â”€ Splash Screen â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ -->
    <div id="splash" role="status" aria-label="Loading...">
        <img id="splash-logo" src="/images/logo.png" alt="HEALTIVA" />
        <svg id="splash-spinner" viewBox="0 0 44 44">
            <circle cx="22" cy="22" r="18"/>
            <circle class="track" cx="22" cy="22" r="18"/>
        </svg>
        <div id="splash-bar-wrap"><div id="splash-bar"></div></div>
    </div>

    @inertia

    <script>
        // Hide splash after page load or max 2.2s (whichever first)
        (function() {
            var splash = document.getElementById('splash');
            function hideSplash() {
                if (splash) {
                    splash.classList.add('hide');
                    setTimeout(function() {
                        if (splash && splash.parentNode) {
                            splash.parentNode.removeChild(splash);
                        }
                    }, 500);
                }
            }
            var timer = setTimeout(hideSplash, 2200);
            window.addEventListener('load', function() {
                clearTimeout(timer);
                setTimeout(hideSplash, 400); // small delay after load for smoothness
            });
        })();
    </script>
</body>
</html>
