<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puwinter — Platform Belajar Bahasa Inggris Terbaik Indonesia</title>
    <meta name="description" content="Belajar bahasa Inggris lebih cerdas bersama Puwinter. Live class, practice test, dan pembahasan latihan bersama tutor terbaik.">
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
            top: 18px;
            left: 50%;
            transform: translateX(-50%);
            width: min(1180px, 92%);
            height: 74px;
            background: rgba(8, 11, 22, .78);
            backdrop-filter: blur(22px);
            border: 1px solid rgba(255, 255, 255, .11);
            border-radius: 26px;
            z-index: 80;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 14px 0 24px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, .28)
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
            grid-template-columns: .9fr 1.2fr .9fr;
            gap: 20px;
            align-items: stretch;
            margin-top: 42px
        }

        .pricing-card {
            background: #fff;
            color: #08111f;
            border-radius: 34px;
            padding: 32px;
            border: 1px solid rgba(255, 255, 255, .08)
        }

        .pricing-card.popular {
            background: linear-gradient(180deg, #5337ec, #0f172a);
            color: white;
            transform: scale(1.04)
        }

        .popular-tag {
            display: inline-block;
            background: #F59E0B;
            color: #111827;
            padding: 7px 14px;
            border-radius: 999px;
            font-weight: 900;
            font-size: 11px;
            margin-bottom: 16px
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
            font-size: 50px;
            font-weight: 900;
            margin: 12px 0
        }

        .pricing-price sup {
            font-size: 18px
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
            margin: 24px 0
        }

        .pricing-features li {
            padding: 10px 0;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px
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
            padding: 15px;
            border: 0
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

        @media(max-width:980px) {
            nav {
                left: 0;
                right: 0;
                bottom: auto;
                width: auto;
                height: 72px;
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
    </style>
</head>

<body>
    <nav id="navbar"><a href="{{ url('/') }}" class="nav-logo"><img src="{{ asset('images/logo.png') }}" alt="Puwinter"></a>
        <ul class="nav-links">
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
                <div class="hero-badge"><span class="dot"></span>The #1 English Online Tutoring Platform in Indonesia</div>
                <h1>Master English with Indonesia’s <br> <span class="highlight">#1 Online Tutoring Platform</span></h1>
                <p>We guide students in mastering English based on their individual learning needs.</p>
                <div class="hero-actions"><a href="{{ route('register') }}" class="btn-hero">Mulai Belajar Gratis <i class="fas fa-arrow-right"></i></a><a href="#fitur" class="btn-hero-ghost"><i class="fas fa-play-circle"></i> Lihat Fitur</a></div>
                <div class="hero-trust">
                    <div class="trust-avatars"><span>A</span><span>R</span><span>D</span><span>N</span><span style="background:#5337ec;">+</span></div>
                    <div class="trust-text">Bergabung dengan Puwinter</div>
                </div>
            </div>
            <div class="hero-right">
                <div class="hero-stack">
                    <div class="float-card">
                        <div class="float-title">Live Class Berlangsung</div>
                        <p>142 peserta online</p><strong>English Grammar — Tenses & Speaking Practice</strong>
                    </div>
                    <div class="float-card">
                        <div class="float-title">Progress Belajar Minggu Ini <span style="float:right;color:#10B981;">+36%</span></div>
                        <div class="progress"><span></span></div>
                        <p>Peringkat: <strong>128</strong> dari {{ number_format($stats['total_students']) }} peserta</p>
                    </div>
                    <div class="float-card">
                        <div class="float-title">Pencapaian Baru!</div>
                        <p>Rising Star 🌟</p><strong>Naik 100 peringkat hari ini</strong>
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
            <div class="stat-desc">Materi English premium</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">{{ $displayKelas }}<span></span></div>
            <div class="stat-desc">Kelas tersedia</div>
        </div>
    </div>
    <div id="fitur" class="section">
        <div class="section-label">Featured Programs</div>
        <h2 class="section-title">Everything You Need to Learn English, <br> <span class="highlight">All in One Place.</span></h2>
        <p class="section-desc">Dari live class interaktif hingga analisis belajar detail — semua tersedia dalam satu platform.</p>
        <div class="feature-grid">@foreach([
            ['icon'=>'fa-video','color'=>'rgba(83,55,236,0.15)','icolor'=>'#9f91ff','title'=>'Live Class Interaktif','desc'=>'Belajar langsung bersama mentor terbaik via Zoom. Tanya jawab real-time dan rekaman tersedia setelahnya.'],
            ['icon'=>'fa-bullseye','color'=>'rgba(239,68,68,0.15)','icolor'=>'#F87171','title'=>'English Literacy Practice Test','desc'=>'Simulasi latihan bahasa Inggris dengan timer, navigasi soal, dan hasil skor instan setelah submit.'],
            ['icon'=>'fa-lightbulb','color'=>'rgba(245,158,11,0.15)','icolor'=>'#FBBF24','title'=>'Pembahasan Lengkap','desc'=>'Setiap latihan ada pembahasan teks dan video tutor. Pahami konsep bahasa Inggris, bukan hanya hafal jawaban.'],
            ['icon'=>'fa-file-pdf','color'=>'rgba(16,185,129,0.15)','icolor'=>'#34D399','title'=>'English PDF Materials Premium','desc'=>'245+ dokumen belajar ringkas dan terstruktur. Download dan belajar kapan saja, di mana saja.'],
            ['icon'=>'fa-chart-bar','color'=>'rgba(83,55,236,0.15)','icolor'=>'#9f91ff','title'=>'Leaderboard & Ranking','desc'=>'Pantau progresmu di antara ribuan siswa bahasa Inggris lain. Filter per sekolah, kota, atau provinsi.'],
            ['icon'=>'fa-chart-line','color'=>'rgba(139,124,246,0.15)','icolor'=>'#8b7cf6','title'=>'Analisis Belajar Detail','desc'=>'Grafik progres, distribusi waktu belajar, dan rekomendasi latihan personal berdasarkan hasil practice test.'],
            ] as $f)<div class="feature-card reveal">
                <div class="feature-icon" style="background:{{ $f['color'] }};"><i class="fas {{ $f['icon'] }}" style="color:{{ $f['icolor'] }};"></i></div>
                <h3>{{ $f['title'] }}</h3>
                <p>{{ $f['desc'] }}</p>
            </div>@endforeach</div>
    </div>
    <div id="cara-kerja" class="steps-section">
        <div class="section-label">Cara Kerja</div>
        <h2 class="section-title">Mulai dalam <span class="highlight">4 langkah mudah</span></h2>
        <div class="steps-grid">@foreach([['n'=>'1','title'=>'Daftar Gratis','desc'=>'Buat akun dalam 30 detik. Tidak perlu kartu kredit.'],['n'=>'2','title'=>'Pilih Materi','desc'=>'Akses ratusan materi dan latihan bahasa Inggris sesuai kebutuhan belajarmu.'],['n'=>'3','title'=>'Ikuti Live Class','desc'=>'Bergabung ke live class dan tanya langsung ke mentor.'],['n'=>'4','title'=>'Pantau Progress','desc'=>'Cek leaderboard dan analisis belajarmu secara berkala.']] as $s)<div class="step-item reveal">
                <div class="step-number">{{ $s['n'] }}</div>
                <h3>{{ $s['title'] }}</h3>
                <p>{{ $s['desc'] }}</p>
            </div>@endforeach</div>
    </div>
    <div id="harga" class="pricing-section">
        <div class="section-label">Harga</div>
        <h2 class="section-title">Investasi terbaik untuk <span class="highlight">masa depanmu</span></h2>
        <p class="section-desc">Mulai gratis, upgrade kapan saja. Garansi uang kembali 7 hari.</p>
        <div class="pricing-grid">@forelse($plans as $plan)
            @php
            $priceK = $plan->price >= 1000
            ? number_format($plan->price / 1000, 0) . 'K'
            : number_format($plan->price);
            $discount = $plan->original_price > $plan->price
            ? ' · Hemat ' . $plan->discountPercentage() . '%'
            : '';
            @endphp<div class="pricing-card {{ $plan->is_popular ? 'popular' : '' }} reveal">@if($plan->is_popular)<div class="popular-tag">PALING POPULER</div>@endif<div class="pricing-name">{{ $plan->name }}</div>
                <div class="pricing-price"><sup>Rp</sup>{{ $priceK }}</div>
                <div class="pricing-period">/ {{ $plan->duration_months }} bulan</div>@if($plan->original_price > $plan->price)<div class="pricing-strike">Rp {{ number_format($plan->original_price) }}{{ $discount }}</div>@endif @if($plan->bonus)<div style="font-size:12px;color:var(--accent);font-weight:800;margin:6px 0;">🎁 {{ $plan->bonus }}</div>@endif @if($plan->features)<ul class="pricing-features">@foreach($plan->features as $f)<li><i class="fas fa-check-circle"></i> {{ $f }}</li>@endforeach</ul>@endif<a href="{{ route('register') }}" class="btn-pricing {{ $plan->is_popular ? 'btn-pricing-filled' : 'btn-pricing-outline' }}">{{ $plan->is_popular ? 'Pilih Paket Ini' : 'Mulai Sekarang' }}</a>
            </div>@empty<div class="pricing-card reveal" style="grid-column:1/-1;text-align:center;padding:40px;">
                <p style="color:#94A3B8;">Paket harga belum tersedia. Hubungi admin.</p>
            </div>@endforelse</div>
        <div style="margin-top:34px;color:#94A3B8;font-size:13.5px" class="reveal"><i class="fas fa-shield-halved" style="color:#10B981;"></i> Garansi uang kembali 7 hari — tidak puas, kami kembalikan 100%.</div>
    </div>
    <div id="testimoni" class="testi-section">
        <div class="section-label">Testimoni</div>
        <h2 class="section-title">Kata mereka yang sudah <span class="highlight">berhasil</span></h2>
        <div class="testi-grid">@foreach([
            ['initial'=>'A','name'=>'Aditya Pratama','info'=>'Diterima UI Teknik Informatika 2024','color'=>'#5337ec','text'=>'"Puwinter benar-benar game changer. Live class-nya interaktif banget, bisa langsung tanya kalau ada yang gak ngerti. Skor tryout saya naik 120 poin dalam 2 bulan!"'],
            ['initial'=>'S','name'=>'Siti Rahayu','info'=>'Diterima ITB Teknik Kimia 2024','color'=>'#5337ec','text'=>'"Awalnya ragu karena harganya murah, tapi kualitasnya melebihi ekspektasi. Pembahasannya detail dan tutor-nya sabar banget neranginnya."'],
            ['initial'=>'R','name'=>'Rafi Ahmad','info'=>'Diterima Unpad Kedokteran 2024','color'=>'#059669','text'=>'"Yang bikin beda dari platform lain adalah analisis belajarnya. Saya tau persis bagian mana yang masih lemah dan harus diperkuat. Highly recommended!"'],
            ['initial'=>'N','name'=>'Nadia Putri','info'=>'Diterima UGM Akuntansi 2024','color'=>'#DC2626','text'=>'"Fitur leaderboard-nya bikin semangat belajar. Seru aja ngeliat nama sendiri naik terus. Plus materi bahasa Inggris PDF-nya lengkap dan mudah dipahami."'],
            ['initial'=>'D','name'=>'Dimas Kurniawan','info'=>'Diterima ITS Teknik Sipil 2024','color'=>'#0891B2','text'=>'"Practice test-nya mirip banget dengan format latihan bahasa Inggris intensif. Saat evaluasi akhir, saya lebih siap karena sudah terbiasa dengan format soal dan manajemen waktu."'],
            ['initial'=>'F','name'=>'Farah Nabila','info'=>'Diterima Unair Psikologi 2024','color'=>'#D97706','text'=>'"Support tim-nya responsif dan ramah. Pernah ada masalah teknis, langsung dibantu dalam hitungan menit. Pengalaman belajarnya nyaman banget."'],
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
                <h4>Platform</h4><a href="#">Live Class</a><a href="#">Tryout</a><a href="#">Practice Bank</a><a href="#">English PDF Materials</a><a href="#">Leaderboard</a>
            </div>
            <div>
                <h4>Perusahaan</h4><a href="#">Tentang Kami</a><a href="#">Blog</a><a href="#">Karir</a><a href="#">Hubungi Kami</a>
            </div>
            <div>
                <h4>Legal</h4><a href="#">Syarat & Ketentuan</a><a href="#">Kebijakan Privasi</a><a href="#">Kebijakan Refund</a>
            </div>
        </div>
        <div class="footer-bottom"><span>© {{ date('Y') }} Puwinter. All rights reserved.</span>
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
</body>

</html>