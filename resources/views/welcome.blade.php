<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.frontend-seo')
    @include('partials.frontend-tracking-head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #5337ec;
            --primary-d: #4328d6;
            --accent: #8b7cf6;
            --gold: #F59E0B;
            --dark: #080b16;
            --panel: #111827;
            --text: #E2E8F0;
            --muted: #94A3B8
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        html {
            scroll-behavior: smooth
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #080b16;
            color: var(--text);
            overflow-x: hidden
        }

        a {
            text-decoration: none;
            color: inherit
        }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: .6s
        }

        .reveal.visible {
            opacity: 1;
            transform: none
        }

        body:before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 20% 10%, rgba(83, 55, 236, .22), transparent 32%), radial-gradient(circle at 85% 20%, rgba(139, 124, 246, .16), transparent 28%), linear-gradient(rgba(255, 255, 255, .025) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .025) 1px, transparent 1px);
            background-size: auto, auto, 56px 56px, 56px 56px;
            pointer-events: none
        }

        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            transform: none;
            width: 100%;
            height: 74px;
            background: rgba(8, 11, 22, .94);
            backdrop-filter: blur(22px);
            border: 0;
            border-bottom: 1px solid rgba(255, 255, 255, .11);
            border-radius: 0;
            z-index: 80;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 max(24px, calc((100vw - 1180px) / 2));
            box-shadow: 0 12px 40px rgba(0, 0, 0, .28)
        }

        nav.scrolled {
            background: rgba(8, 11, 22, .94);
            box-shadow: 0 18px 60px rgba(0, 0, 0, .38)
        }

        .nav-logo {
            display: flex;
            align-items: center
        }

        .nav-logo img {
            width: 154px;
            height: auto;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none
        }

        .nav-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 44px;
            padding: 0 16px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 800;
            color: #cbd5e1;
            transition: .18s
        }

        .nav-links a:hover {
            background: rgba(83, 55, 236, .18);
            color: white
        }

        .nav-cta {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .btn-ghost,
        .btn-primary {
            height: 46px;
            padding: 0 18px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 900;
            transition: .18s
        }

        .btn-ghost {
            border: 1px solid rgba(255, 255, 255, .13);
            background: rgba(255, 255, 255, .05);
            color: white
        }

        .btn-primary {
            background: #5337ec;
            color: white;
            box-shadow: 0 14px 32px rgba(83, 55, 236, .32)
        }

        .btn-ghost:hover,
        .btn-primary:hover {
            transform: translateY(-2px)
        }

        main,
        .footer {
            margin-left: 0
        }

        .hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            align-items: stretch;
            padding-top: 0
        }

        .hero-left {
            padding: 150px 6vw 90px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative
        }

        .hero-badge {
            display: inline-flex;
            width: max-content;
            gap: 10px;
            align-items: center;
            padding: 9px 15px;
            border: 1px solid rgba(139, 124, 246, .28);
            border-radius: 12px;
            background: rgba(139, 124, 246, .08);
            color: #7dd3fc;
            font-weight: 800;
            font-size: 13px;
            margin-bottom: 26px
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e
        }

        .hero h1 {
            font-family: Sora, sans-serif;
            font-size: clamp(42px, 6vw, 78px);
            line-height: .98;
            letter-spacing: -2.8px;
            color: white;
            margin-bottom: 26px
        }

        .highlight {
            color: #8b7cf6
        }

        .hero p {
            max-width: 560px;
            color: #94a3b8;
            line-height: 1.85;
            font-size: 18px;
            margin-bottom: 34px
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap
        }

        .btn-hero,
        .btn-hero-ghost,
        .btn-pricing {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border-radius: 16px;
            font-weight: 900;
            transition: .2s
        }

        .btn-hero {
            padding: 17px 24px;
            background: white;
            color: #080b16
        }

        .btn-hero:hover {
            transform: translateY(-3px)
        }

        .btn-hero-ghost {
            padding: 17px 24px;
            border: 1px solid rgba(255, 255, 255, .12);
            background: rgba(255, 255, 255, .05);
            color: white
        }

        .hero-trust {
            margin-top: 42px;
            display: flex;
            gap: 14px;
            align-items: center
        }

        .trust-avatars {
            display: flex
        }

        .trust-avatars span {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: #5337ec;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: -8px;
            border: 2px solid #080b16;
            font-weight: 900
        }

        .trust-avatars span:first-child {
            margin-left: 0
        }

        .trust-text {
            color: #94a3b8;
            font-size: 14px
        }

        .hero-right {
            background: linear-gradient(160deg, #5337ec, #111827 55%, #8b7cf6);
            padding: 150px 5vw 60px;
            display: flex;
            align-items: center
        }

        .hero-stack {
            width: 100%;
            display: grid;
            gap: 18px;
            transform: rotate(-2deg)
        }

        .float-card {
            background: rgba(255, 255, 255, .92);
            color: #0f172a;
            border-radius: 28px;
            padding: 24px;
            box-shadow: 0 28px 80px rgba(0, 0, 0, .24)
        }

        .float-card:nth-child(2) {
            margin-left: 52px;
            background: #0f172a;
            color: white
        }

        .float-card:nth-child(3) {
            margin-right: 42px
        }

        .float-title {
            font-family: Sora;
            font-weight: 900;
            margin-bottom: 8px
        }

        .progress {
            height: 12px;
            background: rgba(148, 163, 184, .18);
            border-radius: 999px;
            overflow: hidden;
            margin: 14px 0
        }

        .progress span {
            display: block;
            height: 100%;
            width: 78%;
            background: linear-gradient(90deg, #5337ec, #8b7cf6)
        }

        .stats-bar {
            margin-left: 0;
            padding: 26px 5vw;
            background: #0f172a;
            border-top: 1px solid rgba(255, 255, 255, .07);
            border-bottom: 1px solid rgba(255, 255, 255, .07);
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px
        }

        .stat-item {
            border-left: 3px solid #5337ec;
            padding: 14px 18px;
            background: rgba(255, 255, 255, .035);
            border-radius: 18px
        }

        .stat-number {
            font-family: Sora;
            font-size: 36px;
            font-weight: 900;
            color: white
        }

        .stat-desc {
            color: #94a3b8;
            font-size: 13px
        }

        .section,
        .steps-section,
        .pricing-section,
        .testi-section,
        .cta-section {
            margin-left: 0;
            padding: 100px 6vw
        }

        .section-label {
            display: inline-block;
            color: #8b7cf6;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            font-size: 12px;
            margin-bottom: 16px
        }

        .section-title {
            font-family: Sora;
            font-size: clamp(32px, 4vw, 54px);
            line-height: 1.08;
            color: white;
            margin-bottom: 16px
        }

        .section-desc {
            max-width: 640px;
            color: #94a3b8;
            line-height: 1.8;
            margin-bottom: 44px
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 18px
        }

        .feature-card {
            grid-column: span 4;
            min-height: 240px;
            background: linear-gradient(180deg, rgba(255, 255, 255, .07), rgba(255, 255, 255, .035));
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 30px;
            padding: 28px
        }

        .feature-card:nth-child(1),
        .feature-card:nth-child(6) {
            grid-column: span 6
        }

        .feature-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 22px
        }

        .feature-card h3 {
            font-family: Sora;
            font-size: 20px;
            color: white;
            margin-bottom: 12px
        }

        .feature-card p {
            color: #94a3b8;
            line-height: 1.75;
            font-size: 14px
        }

        .steps-section {
            background: #eeeaff;
            color: #08111f
        }

        .steps-section .section-title {
            color: #08111f
        }

        .steps-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            margin-top: 48px;
            counter-reset: step
        }

        .step-item {
            display: grid;
            grid-template-columns: 110px 240px 1fr;
            gap: 24px;
            align-items: center;
            padding: 28px 0;
            border-bottom: 1px solid rgba(15, 23, 42, .12)
        }

        .step-number {
            width: 74px;
            height: 74px;
            border-radius: 24px;
            background: #5337ec;
            color: white;
            font-family: Sora;
            font-size: 30px;
            font-weight: 900;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .step-item h3 {
            font-family: Sora;
            font-size: 24px
        }

        .step-item p {
            color: #475569;
            line-height: 1.75
        }

        .pricing-section {
            background: #080b16
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            align-items: stretch;
            margin-top: 42px
        }

        .pricing-card {
            position: relative;
            background: #fff;
            color: #08111f;
            border-radius: 20px;
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, .08)
        }

        .pricing-card.popular {
            background: linear-gradient(180deg, #5337ec, #0f172a);
            color: white
        }

        .popular-tag {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 2;
            display: inline-block;
            background: #F59E0B;
            color: #111827;
            padding: 4px 9px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 9px;
            letter-spacing: .3px;
            box-shadow: 0 2px 8px rgba(0,0,0,.18)
        }

        .pricing-name {
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px
        }

        .pricing-price {
            font-family: Sora;
            font-size: 34px;
            font-weight: 900;
            margin: 8px 0
        }

        .pricing-price sup {
            font-size: 15px
        }

        .pricing-period,
        .pricing-strike {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 9px
        }

        .popular .pricing-period,
        .popular .pricing-strike {
            color: #ddd6ff
        }

        .pricing-features {
            list-style: none;
            margin: 14px 0
        }

        .pricing-features li {
            padding: 6px 0;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12.5px
        }

        .popular .pricing-features li {
            color: #eeeaff;
            border-color: rgba(255, 255, 255, .12)
        }

        .pricing-features i {
            color: #22c55e;
            margin-right: 8px
        }

        .btn-pricing {
            width: 100%;
            padding: 11px;
            border: 0;
            font-size: 13px
        }

        .btn-pricing-filled {
            background: white;
            color: #08111f
        }

        .btn-pricing-outline {
            background: #08111f;
            color: white
        }

        .testi-section {
            background: #0f172a
        }

        .testi-grid {
            columns: 3 280px;
            column-gap: 18px
        }

        .testi-card {
            break-inside: avoid;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 28px;
            padding: 24px;
            margin-bottom: 18px
        }

        .testi-stars {
            color: #F59E0B;
            margin-bottom: 14px
        }

        .testi-text {
            color: #cbd5e1;
            line-height: 1.75;
            font-style: italic;
            margin-bottom: 18px
        }

        .testi-author {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .testi-avatar {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900
        }

        .testi-name {
            font-weight: 900;
            color: white
        }

        .testi-info {
            font-size: 12px;
            color: #94a3b8
        }

        .cta-box {
            border-radius: 38px;
            padding: 60px;
            background: linear-gradient(135deg, #fff, #eeeaff);
            color: #08111f;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 28px;
            align-items: center
        }

        .cta-box h2 {
            font-family: Sora;
            font-size: 44px;
            margin-bottom: 12px
        }

        .cta-box p {
            color: #475569
        }

        .cta-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap
        }

        .cta-box .btn-hero {
            background: #5337ec;
            color: white
        }

        .cta-box .btn-hero-ghost {
            background: white;
            color: #08111f;
            border-color: #cbd5e1
        }

        .footer {
            padding: 70px 6vw 34px;
            background: #080b16;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 46px
        }

        .footer-brand img {
            width: 160px;
            filter: brightness(0) invert(1)
        }

        .footer-brand p {
            color: #94a3b8;
            line-height: 1.7;
            margin-top: 16px
        }

        .footer-links {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px
        }

        .footer-links h4 {
            color: #64748b;
            text-transform: uppercase;
            font-size: 12px;
            margin-bottom: 14px
        }

        .footer-links a {
            display: block;
            color: #94a3b8;
            margin-bottom: 10px;
            font-size: 14px
        }

        .footer-bottom {
            grid-column: 1/-1;
            border-top: 1px solid rgba(255, 255, 255, .08);
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            color: #64748b;
            font-size: 13px
        }

        .social-links {
            display: flex;
            gap: 10px
        }

        .social-links a {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: rgba(255, 255, 255, .06);
            display: flex;
            align-items: center;
            justify-content: center
        }

        @media(max-width:1100px) {
            .pricing-grid {
                grid-template-columns: 1fr 1fr
            }
        }

        @media(max-width:980px) {
            nav {
                top: 0;
                left: 0;
                right: 0;
                bottom: auto;
                width: 100%;
                height: 72px;
                border-radius: 0;
                flex-direction: row
            }

            .nav-logo img {
                width: 130px
            }

            .nav-links {
                display: none
            }

            .nav-cta {
                margin-left: auto;
                flex-direction: row
            }

            .btn-ghost,
            .btn-primary {
                width: auto;
                padding: 0 18px;
                font-size: 13px
            }

            .btn-ghost:before,
            .btn-primary:before {
                display: none
            }

            main,
            .footer,
            .stats-bar,
            .section,
            .steps-section,
            .pricing-section,
            .testi-section,
            .cta-section {
                margin-left: 0
            }

            .hero {
                grid-template-columns: 1fr
            }

            .hero-right {
                padding: 40px 6vw
            }

            .stats-bar,
            .pricing-grid {
                grid-template-columns: 1fr 1fr
            }

            .feature-card,
            .feature-card:nth-child(1),
            .feature-card:nth-child(6) {
                grid-column: span 6
            }

            .step-item {
                grid-template-columns: 90px 1fr
            }

            .step-item p {
                grid-column: 2
            }

            .cta-box {
                grid-template-columns: 1fr
            }

            .footer {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:640px) {
            .hero-left {
                padding: 110px 6vw 50px
            }

            .stats-bar,
            .pricing-grid {
                grid-template-columns: 1fr
            }

            .feature-card,
            .feature-card:nth-child(1),
            .feature-card:nth-child(6) {
                grid-column: span 12
            }

            .step-item {
                grid-template-columns: 1fr
            }

            .step-item p {
                grid-column: auto
            }

            .pricing-card.popular {
                transform: none
            }

            .footer-links {
                grid-template-columns: 1fr
            }

            .footer-bottom {
                flex-direction: column;
                gap: 18px
            }

            .cta-box {
                padding: 34px 22px
            }

            .cta-box h2 {
                font-size: 30px
            }
        }

        @media(max-width:860px) {
            nav {
                top: 10px;
                width: 94%;
                height: auto;
                min-height: 68px;
                padding: 12px 14px;
                border-radius: 22px;
                flex-wrap: wrap;
                gap: 10px
            }

            .nav-logo img {
                width: 132px
            }

            .nav-links {
                order: 3;
                width: 100%;
                justify-content: center;
                gap: 4px
            }

            .nav-links a {
                height: 36px;
                padding: 0 10px;
                font-size: 12px
            }

            .nav-cta {
                margin-left: auto
            }

            .btn-ghost,
            .btn-primary {
                height: 40px;
                padding: 0 12px;
                font-size: 12px
            }

            .hero {
                grid-template-columns: 1fr
            }

            .hero-left {
                padding: 165px 6vw 60px
            }

            .hero-right {
                padding: 40px 6vw 70px
            }

            .stats-bar {
                grid-template-columns: repeat(2, 1fr)
            }

            .feature-card,
            .feature-card:nth-child(1),
            .feature-card:nth-child(6) {
                grid-column: span 12
            }

            .step-item {
                grid-template-columns: 80px 1fr;
                gap: 16px
            }

            .step-item p {
                grid-column: 2
            }

            .pricing-grid {
                grid-template-columns: 1fr
            }

            .pricing-card.popular {
                transform: none
            }

            .cta-box {
                grid-template-columns: 1fr
            }

            .footer {
                grid-template-columns: 1fr
            }

            .footer-links {
                grid-template-columns: 1fr
            }
        }
    

/* =========================================================
   MOBILE RESPONSIVE FIXES - Added revision
   ========================================================= */
.intro-section {
    padding: 92px 6%;
    background: #0b1020;
}

.video-frame {
    position: relative;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    border-radius: 24px;
    background: #050711;
    border: 1px solid rgba(139,124,246,.3);
    box-shadow: 0 24px 70px rgba(0,0,0,.38);
}

.video-frame iframe,
.video-frame video { width: 100%; height: 100%; border: 0; display: block; object-fit: cover; }
.video-mask-top { position:absolute;top:0;left:0;right:0;height:58px;z-index:5;pointer-events:auto;background:linear-gradient(180deg,rgba(0,0,0,.78),rgba(0,0,0,.20),transparent); }
.video-mask-corner { position:absolute;right:0;bottom:0;width:190px;height:58px;z-index:5;pointer-events:auto;background:linear-gradient(270deg,rgba(0,0,0,.88),rgba(0,0,0,.35),transparent); }
.video-mask-left-corner { position:absolute;left:0;bottom:0;width:130px;height:58px;z-index:5;pointer-events:auto;background:linear-gradient(90deg,rgba(0,0,0,.88),rgba(0,0,0,.35),transparent); }
.video-expand-btn { position:absolute;right:12px;bottom:12px;z-index:6;width:38px;height:38px;border:0;border-radius:10px;background:rgba(15,23,42,.9);color:#fff;display:grid;place-items:center;cursor:pointer;box-shadow:0 8px 20px rgba(0,0,0,.25); }
.video-expand-btn:hover { background:#5337ec; }
.video-frame:fullscreen { width:100vw;height:100vh;aspect-ratio:auto;border-radius:0;background:#000; }
.video-frame:fullscreen iframe,
.video-frame:fullscreen video { border-radius:0; }

.intro-copy { max-width: 760px; margin: 0 auto 28px; text-align: center; }
.intro-copy h2 { font-size: clamp(28px, 3vw, 43px); line-height: 1.15; margin-bottom: 14px; }
.intro-copy > p { color: #94A3B8; line-height: 1.75; }
.demo-video-grid { max-width:1180px;margin:0 auto;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:28px 20px; }
.demo-video-card { min-width:0;display:flex;flex-direction:column; }
.demo-thumbnail { position:relative;aspect-ratio:16/9;overflow:hidden;border-radius:16px;background:linear-gradient(135deg,#171b30,#0b0f1f);border:1px solid rgba(148,163,184,.14);cursor:pointer;box-shadow:0 16px 38px rgba(0,0,0,.24); }
.demo-thumbnail img { width:100%;height:100%;display:block;object-fit:cover;transition:transform .3s ease; }
.demo-video-card:hover .demo-thumbnail img { transform:scale(1.035); }
.demo-thumbnail-placeholder { width:100%;height:100%;display:grid;place-items:center;background:radial-gradient(circle at 50% 45%,rgba(83,55,236,.38),transparent 34%),linear-gradient(135deg,#171b30,#090c17);color:rgba(255,255,255,.38);font-size:42px; }
.demo-thumbnail:after { content:"";position:absolute;inset:0;background:linear-gradient(180deg,transparent 52%,rgba(0,0,0,.62));pointer-events:none; }
.demo-play { position:absolute;z-index:2;left:50%;top:50%;transform:translate(-50%,-50%);width:62px;height:44px;border:0;border-radius:13px;background:#ff0033;color:#fff;display:grid;place-items:center;font-size:18px;cursor:pointer;box-shadow:0 12px 30px rgba(0,0,0,.34);transition:.2s; }
.demo-thumbnail:hover .demo-play { transform:translate(-50%,-50%) scale(1.08);background:#ff1748; }
.demo-category-badge { position:absolute;z-index:2;left:10px;bottom:10px;padding:5px 9px;border-radius:7px;background:rgba(8,11,22,.86);border:1px solid rgba(255,255,255,.14);color:#fff;font-size:10px;font-weight:800;backdrop-filter:blur(8px); }
.demo-video-info { padding:13px 2px 0;display:flex;flex:1;flex-direction:column; }
.demo-video-info h3 { color:#fff;font:700 15px Sora,sans-serif;line-height:1.45;margin-bottom:6px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
.demo-video-info p { color:#94A3B8;font-size:12px;line-height:1.55;margin-bottom:13px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }
.demo-free-button { width:100%;min-height:48px;margin-top:auto;border:1px solid rgba(139,124,246,.55);border-radius:12px;background:linear-gradient(135deg,#6d4aff,#5337ec);color:#fff;display:flex;align-items:center;justify-content:center;gap:9px;font:800 13px Sora,sans-serif;letter-spacing:.15px;cursor:pointer;box-shadow:0 12px 28px rgba(83,55,236,.24);transition:.2s; }
.demo-free-button:hover { transform:translateY(-2px);box-shadow:0 16px 34px rgba(83,55,236,.34);filter:brightness(1.06); }
.demo-player-modal { position:fixed;inset:0;z-index:9999;padding:24px;display:none;align-items:center;justify-content:center;background:rgba(3,5,12,.9);backdrop-filter:blur(12px); }
.demo-player-modal.open { display:flex; }
.demo-player-dialog { width:min(1040px,94vw); }
.demo-player-head { display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:12px;color:#fff; }
.demo-player-head h3 { font:700 clamp(15px,2vw,20px) Sora,sans-serif;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.demo-player-close { flex:0 0 auto;width:40px;height:40px;border:1px solid rgba(255,255,255,.18);border-radius:50%;background:rgba(255,255,255,.08);color:#fff;font-size:18px;cursor:pointer; }
.demo-player-shell { position:relative;aspect-ratio:16/9;border-radius:18px;overflow:hidden;background:#000;box-shadow:0 30px 90px rgba(0,0,0,.5); }
.demo-player-shell iframe,.demo-player-shell video { width:100%;height:100%;display:block;border:0;object-fit:contain;background:#000; }
.hero-program-copy { max-width: 590px; margin-bottom: 34px; color: #CBD5E1; }
.hero .hero-program-copy p { margin-bottom: 8px; font-size: 17px; line-height: 1.65; color: #CBD5E1; }
.hero-program-list { margin: 0; padding-left: 24px; display: grid; gap: 4px; color: #94A3B8; font-size: 16px; line-height: 1.55; }

@media (max-width: 1050px) {
    .demo-video-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
}

@media (max-width: 768px) {
    .intro-section { padding: 56px 18px; }
    .intro-copy h2 { font-size: 27px; }
    .video-frame { border-radius: 18px; }
    .video-mask-top { height:46px; }
    .video-mask-corner { width:145px;height:48px; }
    .video-mask-left-corner { width:95px;height:48px; }
    .demo-video-grid { grid-template-columns:1fr;gap:15px; }
    .demo-thumbnail { border-radius:13px; }
    .demo-player-modal { padding:14px; }
    .demo-player-dialog { width:100%; }
    .demo-player-shell { border-radius:12px; }
    .hero .hero-program-copy p { font-size: 14px; }
    .hero-program-list { font-size: 13.5px; gap: 3px; }
    body { overflow-x: hidden; }

    nav {
        top: 0;
        left: 0;
        right: 0;
        transform: none;
        width: 100%;
        height: auto;
        min-height: 62px;
        padding: 10px 14px;
        border-radius: 0;
        gap: 10px;
    }

    .nav-logo img {
        width: 118px !important;
        max-width: 36vw;
    }

    .nav-links { display: none !important; }

    .nav-cta {
        margin-left: auto;
        gap: 8px;
        flex-shrink: 0;
    }

    .nav-cta .btn-ghost { display: none !important; }

    .btn-primary,
    .btn-ghost {
        height: auto;
        padding: 10px 12px;
        font-size: 11.5px;
        white-space: nowrap;
    }

    .hero {
        min-height: auto;
        grid-template-columns: 1fr !important;
        display: grid;
    }

    .hero-left {
        padding: 112px 18px 42px;
    }

    .hero-badge {
        max-width: 100%;
        width: auto;
        font-size: 11px;
        line-height: 1.45;
        padding: 8px 12px;
        margin-bottom: 16px;
    }

    .hero h1 {
        font-size: clamp(31px, 10vw, 42px);
        line-height: 1.08;
        letter-spacing: -1.3px;
        margin-bottom: 16px;
    }

    .hero h1 br { display: none; }

    .hero p {
        font-size: 14.5px;
        line-height: 1.7;
        margin-bottom: 24px;
    }

    .hero-actions,
    .cta-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        width: 100%;
    }

    .btn-hero,
    .btn-hero-ghost,
    .btn-pricing {
        width: 100%;
        padding: 13px 16px;
        font-size: 13.5px;
        justify-content: center;
    }

    .hero-trust {
        margin-top: 26px;
        align-items: flex-start;
    }

    .trust-avatars span {
        width: 31px;
        height: 31px;
        font-size: 11px;
        border-radius: 10px;
    }

    .trust-text {
        font-size: 12.5px;
        line-height: 1.5;
    }

    .hero-right {
        padding: 22px 18px 52px;
        background: transparent;
    }

    .hero-stack {
        transform: none;
        gap: 12px;
    }

    .float-card,
    .float-card:nth-child(2),
    .float-card:nth-child(3) {
        margin: 0;
        border-radius: 20px;
        padding: 18px;
    }

    .stats-bar {
        padding: 20px 18px;
        grid-template-columns: repeat(2, minmax(0,1fr)) !important;
        gap: 10px;
    }

    .stat-item {
        padding: 14px;
        border-radius: 16px;
    }

    .stat-number {
        font-size: 26px;
    }

    .stat-desc {
        font-size: 11.5px;
        line-height: 1.35;
    }

    .section,
    .steps-section,
    .pricing-section,
    .testi-section,
    .cta-section {
        padding: 56px 18px;
    }

    .section-title {
        font-size: clamp(25px, 8vw, 34px);
        line-height: 1.16;
        letter-spacing: -0.6px;
    }

    .section-title br { display: none; }

    .section-desc {
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 30px;
    }

    .feature-grid {
        grid-template-columns: 1fr !important;
        gap: 14px;
    }

    .feature-card,
    .feature-card:nth-child(1),
    .feature-card:nth-child(6) {
        grid-column: auto !important;
        min-height: auto;
        padding: 20px;
        border-radius: 20px;
    }

    .feature-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        margin-bottom: 14px;
    }

    .feature-card h3,
    .step-item h3 {
        font-size: 16px;
        line-height: 1.35;
    }

    .feature-card p,
    .step-item p,
    .testi-text {
        font-size: 13px;
        line-height: 1.65;
    }

    .steps-grid,
    .pricing-grid {
        grid-template-columns: 1fr !important;
        gap: 14px;
    }

    .step-item {
        display: block;
        padding: 20px 0;
    }

    .step-number {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        font-size: 24px;
        margin-bottom: 12px;
    }

    .pricing-card,
    .pricing-card.popular {
        transform: none;
        padding: 22px;
        border-radius: 22px;
    }

    .pricing-price {
        font-size: 36px;
    }

    .pricing-features li {
        font-size: 13px;
        line-height: 1.45;
    }

    .testi-grid {
        columns: auto !important;
        display: flex !important;
        overflow-x: auto;
        gap: 14px;
        scroll-snap-type: x mandatory;
        padding-bottom: 12px;
        -webkit-overflow-scrolling: touch;
    }

    .testi-card {
        min-width: 82vw;
        scroll-snap-align: start;
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 0;
    }

    .cta-box {
        grid-template-columns: 1fr !important;
        padding: 32px 20px;
        border-radius: 24px;
        text-align: left;
    }

    .cta-box h2 {
        font-size: clamp(24px, 8vw, 32px);
        line-height: 1.18;
    }

    .cta-box p {
        font-size: 14px;
        line-height: 1.65;
    }

    .footer {
        padding: 42px 18px 28px;
        grid-template-columns: 1fr !important;
        gap: 28px;
    }

    .footer-links {
        grid-template-columns: 1fr !important;
        gap: 18px;
    }

    .footer-bottom {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        line-height: 1.5;
    }
}

@media (max-width: 380px) {
    nav { left: 0; right: 0; }
    .nav-logo img { width: 108px !important; }
    .btn-primary { padding: 9px 10px; font-size: 11px; }
    .hero-left,
    .hero-right,
    .section,
    .steps-section,
    .pricing-section,
    .testi-section,
    .cta-section { padding-left: 14px; padding-right: 14px; }
}

</style>
</head>

<body>
    @include('partials.frontend-tracking-body')
    <nav id="navbar"><a href="{{ url('/') }}" class="nav-logo"><img src="{{ asset('images/logo.png') }}" alt="Puwinter"></a>
        <ul class="nav-links">
            @if($demoVideos->isNotEmpty())<li><a href="#video-demo">Video Demo</a></li>@endif
            <li><a href="#fitur">Fitur</a></li>
            <li><a href="#cara-kerja">Cara Kerja</a></li>
            <li><a href="#harga">Harga</a></li>
            <li><a href="#testimoni">Testimoni</a></li>
        </ul>
        <div class="nav-cta"><a href="{{ route('login') }}" class="btn-ghost">Masuk</a><a href="{{ route('register') }}" class="btn-primary">Daftar Gratis</a></div>
    </nav>@php
    $displayStudents = $stats['total_students'] >= 1000
    ? number_format($stats['total_students'] / 1000, 0) . 'K+'
    : $stats['total_students'] . '+';
    $displaySoal = $stats['total_soal'] >= 1000
    ? number_format($stats['total_soal'] / 1000, 1) . 'K+'
    : $stats['total_soal'] . '+';
    $displayMateri = $stats['total_materi'] . '+';
    $displayKelas = $stats['total_kelas'] . '+';
    @endphp
    <main>
        <section class="hero">
            <div class="hero-left">
                <div class="hero-badge"><span class="dot"></span>Platform Bimbingan Bahasa Inggris Online Terpercaya di Indonesia</div>
                <h1>Kuasai Grammar, Pahami Teks, <br> <span class="highlight">Raih Prestasi</span></h1>
                <div class="hero-program-copy">
                    <p>Program bimbel Puwinter: Pendampingan bimbel:</p>
                    <ol class="hero-program-list">
                        <li>Bahasa Inggris TKA</li>
                        <li>Literasi Bahasa Inggris UTBK SNBT</li>
                        <li>Grammar Dasar &amp; Reading Text</li>
                        <li>Grammar &amp; Reading TOEFL</li>
                    </ol>
                </div>
                <div class="hero-actions"><a href="{{ route('register') }}" class="btn-hero">Mulai Belajar Gratis <i class="fas fa-arrow-right"></i></a><a href="#fitur" class="btn-hero-ghost"><i class="fas fa-play-circle"></i> Lihat Fitur</a></div>
                <div class="hero-trust">
                    <div class="trust-avatars"><span>A</span><span>R</span><span>D</span><span>N</span><span style="background:#5337ec;">+</span></div>
                    <div class="trust-text">Bergabung dengan Puwinter</div>
                </div>
            </div>
            <div class="hero-right">
                <div class="hero-stack">
                    <div class="float-card">
                        <div class="float-title">Kelas Online Berlangsung</div>
                        <p>142 peserta online</p><strong>Grammar Bahasa Inggris — Tenses & Latihan Berbicara</strong>
                    </div>
                    <div class="float-card">
                        <div class="float-title">Progres Belajar Minggu Ini <span style="float:right;color:#10B981;">+36%</span></div>
                        <div class="progress"><span></span></div>
                        <p>Peringkat: <strong>128</strong> dari {{ number_format($stats['total_students']) }} peserta</p>
                    </div>
                    <div class="float-card">
                        <div class="float-title">Pencapaian Baru!</div>
                        <p>Bintang Baru 🌟</p><strong>Naik 100 peringkat hari ini</strong>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <div class="stats-bar">
        <div class="stat-item reveal">
            <div class="stat-number">{{ $displayStudents }}</div>
            <div class="stat-desc">Sedang bergabung</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">{{ $displaySoal }}</div>
            <div class="stat-desc">Latihan + pembahasan</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">{{ $displayMateri }}</div>
            <div class="stat-desc">Materi Bahasa Inggris premium</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">{{ $displayKelas }}<span></span></div>
            <div class="stat-desc">Kelas tersedia</div>
        </div>
    </div>
    @if($demoVideos->isNotEmpty())
    <section class="intro-section" id="video-demo">
        <div class="intro-copy">
            <div class="section-label">Coba Belajar Gratis</div>
            <h2>Video Demo <span class="highlight">Pembelajaran</span></h2>
            <p>Pilih video yang kamu suka dan lihat langsung cara mentor Puwinter menjelaskan materi Bahasa Inggris.</p>
        </div>
        <div class="demo-video-grid">
            @foreach($demoVideos as $demoVideo)
            @php
                $player = $demoVideo->playerData();
                $thumbnail = $demoVideo->thumbnailUrl();
            @endphp
            <article class="demo-video-card reveal">
                <div class="demo-thumbnail" role="button" tabindex="0"
                     data-video-type="{{ $player['type'] }}"
                     data-video-provider="{{ $player['provider'] }}"
                     data-video-url="{{ $player['url'] }}"
                     data-video-title="{{ $demoVideo->title }}"
                     onclick="openDemoPlayer(this)"
                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openDemoPlayer(this)}"
                     aria-label="Tonton gratis: {{ $demoVideo->title }}">
                    @if($thumbnail)
                    <img src="{{ $thumbnail }}" alt="Thumbnail {{ $demoVideo->title }}" loading="lazy">
                    @else
                    <div class="demo-thumbnail-placeholder"><i class="fas fa-film"></i></div>
                    @endif
                    <button type="button" class="demo-play" tabindex="-1" aria-hidden="true"><i class="fas fa-play"></i></button>
                    <span class="demo-category-badge">{{ $demoVideo->categoryLabel() }}</span>
                </div>
                <div class="demo-video-info">
                    <h3>{{ $demoVideo->title }}</h3>
                    @if($demoVideo->description)<p>{{ $demoVideo->description }}</p>@endif
                    <button type="button" class="demo-free-button"
                            data-video-type="{{ $player['type'] }}"
                            data-video-provider="{{ $player['provider'] }}"
                            data-video-url="{{ $player['url'] }}"
                            data-video-title="{{ $demoVideo->title }}"
                            onclick="openDemoPlayer(this)">
                        <i class="fas fa-circle-play"></i> Tonton Gratis
                    </button>
                </div>
            </article>
            @endforeach
        </div>
        <div class="demo-player-modal" id="demoPlayerModal" role="dialog" aria-modal="true" aria-labelledby="demoPlayerTitle" onclick="if(event.target===this)closeDemoPlayer()">
            <div class="demo-player-dialog">
                <div class="demo-player-head">
                    <h3 id="demoPlayerTitle">Video Demo Pembelajaran</h3>
                    <button type="button" class="demo-player-close" onclick="closeDemoPlayer()" aria-label="Tutup video"><i class="fas fa-xmark"></i></button>
                </div>
                <div class="demo-player-shell" id="demoPlayerShell"></div>
            </div>
        </div>
    </section>
    @endif
    <div id="fitur" class="section">
        <div class="section-label">Program Unggulan</div>
        <h2 class="section-title">Semua yang Kamu Butuhkan untuk Belajar Bahasa Inggris, <br> <span class="highlight">dalam Satu Platform.</span></h2>
        <p class="section-desc">Dari kelas online (live) interaktif hingga analisis belajar detail — semua tersedia dalam satu platform.</p>
        <div class="feature-grid">@foreach([
            ['icon'=>'fa-video','color'=>'rgba(83,55,236,0.15)','icolor'=>'#9f91ff','title'=>'Kelas Online (Live) Interaktif','desc'=>'Belajar langsung bersama mentor terbaik via Zoom. Tanya jawab langsung dan rekaman tersedia setelahnya.'],
            ['icon'=>'fa-bullseye','color'=>'rgba(239,68,68,0.15)','icolor'=>'#F87171','title'=>'Latihan Soal Literasi Bahasa Inggris','desc'=>'Simulasi latihan bahasa Inggris dengan timer, navigasi soal, dan hasil skor langsung setelah dikirim.'],
            ['icon'=>'fa-lightbulb','color'=>'rgba(245,158,11,0.15)','icolor'=>'#FBBF24','title'=>'Pembahasan Lengkap','desc'=>'Setiap latihan ada pembahasan teks dan video tutor. Pahami konsep bahasa Inggris, bukan hanya hafal jawaban.'],
            ['icon'=>'fa-file-pdf','color'=>'rgba(16,185,129,0.15)','icolor'=>'#34D399','title'=>'Materi PDF Bahasa Inggris Premium','desc'=>'245+ dokumen belajar ringkas dan terstruktur. Unduh dan belajar kapan saja, di mana saja.'],
            ['icon'=>'fa-chart-bar','color'=>'rgba(83,55,236,0.15)','icolor'=>'#9f91ff','title'=>'Papan Peringkat','desc'=>'Pantau progresmu di antara ribuan siswa bahasa Inggris lain. Saring per sekolah, kota, atau provinsi.'],
            ['icon'=>'fa-chart-line','color'=>'rgba(139,124,246,0.15)','icolor'=>'#8b7cf6','title'=>'Analisis Belajar Detail','desc'=>'Lihat grafik progres dan distribusi waktu belajar secara visual untuk memantau perkembangan dan konsistensi belajar siswa.'],
            ] as $f)<div class="feature-card reveal">
                <div class="feature-icon" style="background:{{ $f['color'] }};"><i class="fas {{ $f['icon'] }}" style="color:{{ $f['icolor'] }};"></i></div>
                <h3>{{ $f['title'] }}</h3>
                <p>{{ $f['desc'] }}</p>
            </div>@endforeach</div>
    </div>
    <div id="cara-kerja" class="steps-section">
        <div class="section-label">Cara Kerja</div>
        <h2 class="section-title">Mulai dalam <span class="highlight">4 langkah mudah</span></h2>
        <div class="steps-grid">@foreach([['n'=>'1','title'=>'Daftar Gratis','desc'=>'Buat akun dalam 30 detik. Tidak perlu kartu kredit.'],['n'=>'2','title'=>'Pilih Materi','desc'=>'Akses materi dan latihan bahasa Inggris sesuai kebutuhan belajarmu.'],['n'=>'3','title'=>'Ikuti Kelas Online','desc'=>'Bergabung ke kelas online dan tanya langsung ke mentor.'],['n'=>'4','title'=>'Pantau Progres','desc'=>'Cek papan peringkat dan analisis belajarmu secara berkala.']] as $s)<div class="step-item reveal">
                <div class="step-number">{{ $s['n'] }}</div>
                <h3>{{ $s['title'] }}</h3>
                <p>{{ $s['desc'] }}</p>
            </div>@endforeach</div>
    </div>
    <div id="harga" class="pricing-section">
        <div class="section-label">Harga</div>
        <h2 class="section-title">Investasi terbaik untuk <span class="highlight">masa depanmu</span></h2>
        <p class="section-desc">Mulai gratis, tingkatkan program kapan saja.</p>
        <div class="pricing-grid">@forelse($plans as $plan)
            @php
            $priceK = $plan->price >= 1000
            ? number_format($plan->price / 1000, 0) . 'K'
            : number_format($plan->price);
            $discount = $plan->original_price > $plan->price
            ? ' · Hemat ' . $plan->discountPercentage() . '%'
            : '';
            @endphp<div class="pricing-card {{ $plan->is_popular ? 'popular' : '' }} reveal">@if($plan->is_popular)<div class="popular-tag">PALING POPULER</div>@endif @if($plan->flyer_image)<div class="flyer-thumb" onclick="openFlyer('{{ asset('storage/'.$plan->flyer_image) }}')" style="margin:0 0 12px;cursor:zoom-in;border-radius:10px;overflow:hidden;border:1px solid rgba(148,163,184,.25);"><img src="{{ asset('storage/'.$plan->flyer_image) }}" alt="Pamflet {{ $plan->name }}" style="width:100%;display:block;height:275px;object-fit:cover;object-position:top;"></div>@endif<div class="pricing-name">{{ $plan->name }}</div>@if($plan->periodLabel())<div style="font-size:11px;color:#94A3B8;font-weight:600;margin-top:2px;"><i class="far fa-calendar"></i> {{ $plan->periodLabel() }}</div>@endif
                <div class="pricing-price"><sup>Rp</sup>{{ $priceK }}</div>
                @if($plan->original_price > $plan->price)<div class="pricing-strike">Rp {{ number_format($plan->original_price) }}{{ $discount }}</div>@endif @if($plan->bonus)<div style="font-size:12px;color:var(--accent);font-weight:800;margin:6px 0;">🎁 {{ $plan->bonus }}</div>@endif @if($plan->features)<ul class="pricing-features">@foreach($plan->features as $f)<li><i class="fas fa-check-circle"></i> {{ $f }}</li>@endforeach</ul>@endif<a href="{{ route('register') }}" class="btn-pricing {{ $plan->is_popular ? 'btn-pricing-filled' : 'btn-pricing-outline' }}">{{ $plan->is_popular ? 'Pilih Program Ini' : 'Daftar Sekarang' }}</a>@if(!is_null($plan->quota))<div style="font-size:11.5px;margin-top:8px;font-weight:600;color:{{ $plan->isQuotaFull() ? '#EF4444' : '#10B981' }};">@if($plan->isQuotaFull())<i class="fas fa-circle-xmark"></i> Kuota penuh@else<i class="fas fa-user-check"></i> Sisa kuota: {{ $plan->remainingQuota() }} dari {{ $plan->quota }}@endif</div>@endif
            </div>@empty<div class="pricing-card reveal" style="grid-column:1/-1;text-align:center;padding:40px;">
                <p style="color:#94A3B8;">Program belum tersedia. Hubungi admin.</p>
            </div>@endforelse</div>
    </div>
    <div id="testimoni" class="testi-section">
        <div class="section-label">Testimoni</div>
        <h2 class="section-title">Kata mereka yang sudah <span class="highlight">berhasil</span></h2>
        <div class="testi-grid">@foreach([
            ['initial'=>'A','name'=>'Aditya Pratama','info'=>'Diterima UI Teknik Informatika 2024','color'=>'#5337ec','text'=>'"Puwinter benar-benar membantu banget. Kelas live-nya interaktif banget, bisa langsung tanya kalau ada yang gak ngerti. Skor tryout saya naik 120 poin dalam 2 bulan!"'],
            ['initial'=>'S','name'=>'Siti Rahayu','info'=>'Diterima ITB Teknik Kimia 2024','color'=>'#5337ec','text'=>'"Awalnya ragu karena harganya murah, tapi kualitasnya melebihi ekspektasi. Pembahasannya detail dan tutor-nya sabar banget neranginnya."'],
            ['initial'=>'R','name'=>'Rafi Ahmad','info'=>'Diterima Unpad Kedokteran 2024','color'=>'#059669','text'=>'"Yang bikin beda dari platform lain adalah analisis belajarnya. Saya tau persis bagian mana yang masih lemah dan harus diperkuat. Sangat direkomendasikan!"'],
            ['initial'=>'N','name'=>'Nadia Putri','info'=>'Diterima UGM Akuntansi 2024','color'=>'#DC2626','text'=>'"Fitur papan peringkatnya bikin semangat belajar. Seru aja ngeliat nama sendiri naik terus. Plus materi bahasa Inggris PDF-nya lengkap dan mudah dipahami."'],
            ['initial'=>'D','name'=>'Dimas Kurniawan','info'=>'Diterima ITS Teknik Sipil 2024','color'=>'#0891B2','text'=>'"Latihannya mirip banget dengan format latihan bahasa Inggris intensif. Saat evaluasi akhir, saya lebih siap karena sudah terbiasa dengan format soal dan manajemen waktu."'],
            ['initial'=>'F','name'=>'Farah Nabila','info'=>'Diterima Unair Psikologi 2024','color'=>'#D97706','text'=>'"Tim dukungannya responsif dan ramah. Pernah ada masalah teknis, langsung dibantu dalam hitungan menit. Pengalaman belajarnya nyaman banget."'],
            ] as $t)<div class="testi-card reveal">
                <div class="testi-stars">@for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor</div>
                <p class="testi-text">{{ $t['text'] }}</p>
                <div class="testi-author">
                    <div class="testi-avatar" style="background:{{ $t['color'] }};">{{ $t['initial'] }}</div>
                    <div>
                        <div class="testi-name">{{ $t['name'] }}</div>
                        <div class="testi-info">{{ $t['info'] }}</div>
                    </div>
                </div>
            </div>@endforeach</div>
    </div>
    <div class="cta-section">
        <div class="cta-box reveal">
            <div>
                <h2>Siap mulai perjalanan belajar bahasa Inggrismu?</h2>
                <p>Daftar gratis sekarang dan mulai belajar bersama {{ number_format($stats['total_students']) }}+ siswa bahasa Inggris lainnya.</p>
            </div>
            <div class="cta-actions"><a href="{{ route('register') }}" class="btn-hero">Daftar Gratis Sekarang <i class="fas fa-arrow-right"></i></a><a href="{{ route('login') }}" class="btn-hero-ghost">Sudah punya akun? Masuk</a></div>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-brand">
            <a href="{{ url('/') }}"><img src="{{ asset('images/logo.png') }}" alt="Puwinter"></a>
            <p>Platform belajar bahasa Inggris terlengkap dan terpercaya, belajar cerdas sukses lebih pasti.</p>
        </div>
        <div class="footer-links">
            <div>
                <h4>Platform</h4><a href="#">Kelas Online</a><a href="#">Tryout</a><a href="#">Bank Soal</a><a href="#">Materi PDF Bahasa Inggris</a><a href="#">Papan Peringkat</a>
            </div>
            <div>
                <h4>Perusahaan</h4><a href="#">Tentang Kami</a><a href="#">Hubungi Kami</a>
            </div>
            <div>
                <h4>Legal</h4><a href="#">Syarat & Ketentuan</a><a href="#">Kebijakan Privasi</a>
            </div>
        </div>
        <div class="footer-bottom"><span>© {{ date('Y') }} Puwinter. Seluruh hak cipta dilindungi.</span>
            <div class="social-links"><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-tiktok"></i></a><a href="#"><i class="fab fa-youtube"></i></a><a href="#"><i class="fab fa-twitter"></i></a></div>
        </div>
    </footer>
    <script>
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 20));
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => entry.target.classList.add('visible'), i * 70);
                    observer.unobserve(entry.target);
                }
            })
        }, {
            threshold: .1
        });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
    </script>
<div id="flyerLightbox" onclick="closeFlyer()" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.85);align-items:center;justify-content:center;padding:24px;cursor:zoom-out;">
    <span style="position:absolute;top:18px;right:26px;color:#fff;font-size:34px;line-height:1;cursor:pointer;font-weight:300;">&times;</span>
    <img id="flyerLightboxImg" src="" alt="Pamflet" style="max-width:92vw;max-height:90vh;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.5);">
</div>
<script>
    function openFlyer(src){var b=document.getElementById('flyerLightbox');document.getElementById('flyerLightboxImg').src=src;b.style.display='flex';document.body.style.overflow='hidden';}
    function closeFlyer(){document.getElementById('flyerLightbox').style.display='none';document.body.style.overflow='';}
    document.addEventListener('keydown',function(e){if(e.key==='Escape')closeFlyer();});
    function openDemoPlayer(trigger){
        var modal=document.getElementById('demoPlayerModal');
        var shell=document.getElementById('demoPlayerShell');
        var title=document.getElementById('demoPlayerTitle');
        if(!modal||!shell)return;
        var type=trigger.dataset.videoType;
        var provider=trigger.dataset.videoProvider;
        var url=trigger.dataset.videoUrl;
        title.textContent=trigger.dataset.videoTitle||'Video Demo Pembelajaran';
        shell.innerHTML='';
        if(type==='embed'){
            var frame=document.createElement('iframe');
            frame.src=url+(url.indexOf('?')===-1?'?':'&')+'autoplay=1';
            frame.title=title.textContent;
            frame.allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
            frame.allowFullscreen=true;
            shell.appendChild(frame);
            if(provider==='youtube'){
                [
                    ['video-mask-top','Area judul dan menu YouTube dilindungi'],
                    ['video-mask-corner','Tombol berbagi dan buka YouTube dilindungi'],
                    ['video-mask-left-corner','Tombol salin tautan dilindungi']
                ].forEach(function(item){
                    var mask=document.createElement('div');
                    mask.className=item[0];
                    mask.title=item[1];
                    mask.setAttribute('aria-hidden','true');
                    mask.oncontextmenu=function(){return false};
                    shell.appendChild(mask);
                });
            }
        }else{
            var video=document.createElement('video');
            video.src=url;
            video.controls=true;
            video.autoplay=true;
            video.setAttribute('controlslist','nodownload noremoteplayback');
            video.setAttribute('disablepictureinpicture','');
            shell.appendChild(video);
        }
        modal.classList.add('open');
        document.body.style.overflow='hidden';
        modal.querySelector('.demo-player-close').focus();
    }
    function closeDemoPlayer(){
        var modal=document.getElementById('demoPlayerModal');
        var shell=document.getElementById('demoPlayerShell');
        if(!modal)return;
        modal.classList.remove('open');
        if(shell)shell.innerHTML='';
        document.body.style.overflow='';
    }
    document.addEventListener('keydown',function(e){if(e.key==='Escape')closeDemoPlayer();});
</script>
</body>

</html>
