<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puwinter — English Learning Platform</title>
    <meta name="description" content="Belajar bahasa Inggris lebih efektif bersama Puwinter. Live class, assessment, dan pembahasan materi bersama tutor terbaik.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #5337ec;
            --primary-d: #4327d8;
            --accent: #7c6cff;
            --ink: #07111f;
            --paper: #f7fbff;
            --muted: #64748B;
            --gold: #F59E0B
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
            background: var(--paper);
            color: #0f172a;
            overflow-x: hidden
        }

        a {
            text-decoration: none;
            color: inherit
        }

        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: .65s
        }

        .reveal.visible {
            opacity: 1;
            transform: none
        }

        nav {
            position: fixed;
            top: 18px;
            left: 50%;
            transform: translateX(-50%);
            width: min(1120px, 92%);
            height: 74px;
            padding: 0 20px;
            background: rgba(255, 255, 255, .82);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 24px;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 24px 80px rgba(2, 8, 23, .08)
        }

        nav.scrolled {
            top: 10px;
            box-shadow: 0 20px 60px rgba(2, 8, 23, .16)
        }

        .nav-logo img,
        .footer-brand img {
            width: 160px;
            height: auto;
            filter: none
        }

        .nav-links {
            display: flex;
            gap: 8px;
            list-style: none;
            background: #f2efff;
            padding: 7px;
            border-radius: 18px
        }

        .nav-links a {
            display: block;
            padding: 10px 14px;
            border-radius: 13px;
            font-size: 13px;
            font-weight: 800;
            color: #334155
        }

        .nav-links a:hover {
            background: white;
            color: var(--primary)
        }

        .nav-cta {
            display: flex;
            gap: 10px
        }

        .btn-ghost,
        .btn-primary,
        .btn-hero,
        .btn-hero-ghost,
        .btn-pricing {
            border-radius: 999px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: .2s
        }

        .btn-ghost {
            padding: 11px 18px;
            border: 1px solid #ede9ff;
            color: #1e293b
        }

        .btn-primary,
        .btn-hero,
        .btn-pricing-filled {
            background: linear-gradient(135deg, var(--primary), #8b7dff);
            color: white;
            box-shadow: 0 14px 32px rgba(83, 55, 236, .28)
        }

        .btn-primary {
            padding: 12px 19px
        }

        .btn-hero {
            padding: 16px 26px
        }

        .btn-hero-ghost {
            padding: 16px 24px;
            background: white;
            border: 1px solid #ede9ff;
            color: #0f172a
        }

        .hero {
            min-height: 100vh;
            padding: 140px 5% 90px;
            background: radial-gradient(circle at 15% 15%, #ede9ff 0, transparent 28%), linear-gradient(135deg, #f8fbff 0%, #f4f1ff 52%, #ffffff 100%);
            position: relative;
            overflow: hidden
        }

        .hero:after {
            content: '';
            position: absolute;
            right: -12%;
            top: 13%;
            width: 55vw;
            height: 55vw;
            border-radius: 50%;
            background: conic-gradient(from 90deg, rgba(83, 55, 236, .22), rgba(124, 108, 255, .08), rgba(245, 158, 11, .16), rgba(83, 55, 236, .22));
            filter: blur(1px)
        }

        .hero-shell {
            position: relative;
            z-index: 2;
            max-width: 1180px;
            margin: auto;
            display: grid;
            grid-template-columns: 1.02fr .98fr;
            gap: 54px;
            align-items: center
        }

        .hero-badge {
            display: inline-flex;
            gap: 10px;
            align-items: center;
            padding: 10px 16px;
            background: white;
            border: 1px solid #ede9ff;
            border-radius: 999px;
            color: var(--primary);
            font-weight: 900;
            font-size: 13px;
            margin-bottom: 22px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, .06)
        }

        .dot {
            width: 9px;
            height: 9px;
            background: #22c55e;
            border-radius: 50%;
            box-shadow: 0 0 0 7px rgba(34, 197, 94, .12)
        }

        h1 {
            font-family: Sora, sans-serif;
            font-size: clamp(38px, 5vw, 70px);
            line-height: 1.02;
            letter-spacing: -2.2px;
            color: #061326;
            margin-bottom: 24px
        }

        .highlight {
            background: linear-gradient(135deg, #5337ec, #6d5dfc);
            -webkit-background-clip: text;
            color: transparent
        }

        .hero p,
        .section-desc {
            color: #64748b;
            line-height: 1.8;
            font-size: 17px
        }

        .hero p {
            max-width: 540px;
            margin-bottom: 34px
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap
        }

        .hero-trust {
            margin-top: 34px;
            display: flex;
            gap: 15px;
            align-items: center
        }

        .trust-avatars {
            display: flex
        }

        .trust-avatars span {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #5337ec;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            font-weight: 900;
            margin-left: -10px
        }

        .trust-avatars span:first-child {
            margin-left: 0
        }

        .trust-text {
            font-size: 14px;
            color: #64748b
        }

        .hero-visual {
            background: #07111f;
            border-radius: 42px;
            padding: 20px;
            box-shadow: 0 40px 100px rgba(2, 8, 23, .28);
            position: relative;
            overflow: hidden
        }

        .hero-visual:before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 10%, rgba(124, 108, 255, .3), transparent 40%)
        }

        .dashboard {
            position: relative;
            z-index: 1;
            background: rgba(15, 23, 42, .9);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 30px;
            padding: 24px;
            color: white
        }

        .dash-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 26px
        }

        .dash-pill {
            background: rgba(239, 68, 68, .16);
            color: #fecaca;
            font-size: 10px;
            font-weight: 900;
            padding: 6px 10px;
            border-radius: 999px
        }

        .class-card {
            background: linear-gradient(135deg, #5337ec, #7c6cff);
            border-radius: 26px;
            padding: 28px;
            margin-bottom: 18px
        }

        .class-card h3 {
            font-family: Sora;
            font-size: 27px;
            margin: 8px 0
        }

        .mini-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px
        }

        .mini-card {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 20px;
            padding: 18px
        }

        .mini-card strong {
            font-family: Sora;
            font-size: 26px;
            color: #fff
        }

        .stat-strip {
            max-width: 1080px;
            margin: -45px auto 70px;
            position: relative;
            z-index: 5;
            background: #061326;
            color: white;
            border-radius: 30px;
            padding: 26px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            box-shadow: 0 26px 70px rgba(2, 8, 23, .22)
        }

        .stat-item {
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, .05)
        }

        .stat-number {
            font-family: Sora;
            font-size: 34px;
            font-weight: 900
        }

        .stat-desc {
            color: #94a3b8;
            font-size: 13px;
            margin-top: 5px
        }

        .section,
        .pricing-section,
        .testi-section,
        .cta-section {
            max-width: 1180px;
            margin: auto;
            padding: 80px 5%
        }

        .section-label {
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: var(--primary);
            font-size: 12px;
            margin-bottom: 14px
        }

        .section-title {
            font-family: Sora;
            font-size: clamp(30px, 4vw, 48px);
            line-height: 1.1;
            letter-spacing: -1.2px;
            margin-bottom: 14px
        }

        .feature-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            grid-auto-rows: minmax(210px, auto);
            gap: 18px;
            margin-top: 42px
        }

        .feature-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 30px;
            padding: 28px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, .06);
            position: relative;
            overflow: hidden
        }

        .feature-card:first-child {
            grid-row: span 2;
            background: #061326;
            color: white
        }

        .feature-card:first-child p {
            color: #94a3b8
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px
        }

        .feature-card h3 {
            font-family: Sora;
            font-size: 19px;
            margin-bottom: 10px
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.75;
            font-size: 14px
        }

        .steps-section {
            padding: 90px 5%;
            background: #061326;
            color: white
        }

        .steps-inner {
            max-width: 1180px;
            margin: auto
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-top: 42px
        }

        .step-item {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 26px;
            padding: 26px
        }

        .step-number {
            font-family: Sora;
            font-size: 46px;
            color: #7c6cff;
            font-weight: 900
        }

        .step-item h3 {
            font-family: Sora;
            margin: 8px 0
        }

        .step-item p {
            color: #94a3b8;
            line-height: 1.7;
            font-size: 14px
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 45px
        }

        .pricing-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 32px;
            padding: 30px;
            box-shadow: 0 18px 50px rgba(15, 23, 42, .06)
        }

        .pricing-card.popular {
            background: #061326;
            color: white;
            transform: translateY(-18px)
        }

        .popular-tag {
            display: inline-block;
            background: #F59E0B;
            color: #111827;
            padding: 7px 13px;
            border-radius: 999px;
            font-weight: 900;
            font-size: 11px;
            margin-bottom: 16px
        }

        .pricing-name {
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            font-size: 12px
        }

        .pricing-price {
            font-family: Sora;
            font-size: 46px;
            font-weight: 900;
            margin: 10px 0
        }

        .pricing-price sup {
            font-size: 18px
        }

        .pricing-period,
        .pricing-strike {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 10px
        }

        .pricing-features {
            list-style: none;
            margin: 24px 0
        }

        .pricing-features li {
            padding: 9px 0;
            color: #64748b;
            font-size: 14px
        }

        .popular .pricing-features li,
        .popular .pricing-period,
        .popular .pricing-strike {
            color: #94a3b8
        }

        .pricing-features i {
            color: #22c55e;
            margin-right: 8px
        }

        .btn-pricing {
            width: 100%;
            padding: 14px;
            border: 0
        }

        .btn-pricing-outline {
            border: 1px solid #cbd5e1;
            background: white;
            color: #0f172a
        }

        .testi-section {
            max-width: none;
            background: #f4f1ff
        }

        .testi-inner {
            max-width: 1180px;
            margin: auto
        }

        .testi-grid {
            display: grid;
            grid-template-columns: repeat(6, 280px);
            gap: 18px;
            overflow-x: auto;
            padding-bottom: 16px
        }

        .testi-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 26px;
            padding: 24px
        }

        .testi-stars {
            color: #F59E0B;
            font-size: 13px;
            margin-bottom: 14px
        }

        .testi-text {
            color: #475569;
            line-height: 1.75;
            font-style: italic;
            font-size: 14px;
            margin-bottom: 18px
        }

        .testi-author {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .testi-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900
        }

        .testi-name {
            font-weight: 900
        }

        .testi-info {
            font-size: 12px;
            color: #64748b
        }

        .cta-box {
            background: linear-gradient(135deg, #5337ec, #0f172a);
            border-radius: 40px;
            padding: 58px;
            text-align: center;
            color: white;
            box-shadow: 0 35px 90px rgba(83, 55, 236, .25)
        }

        .cta-box h2 {
            font-family: Sora;
            font-size: 42px;
            margin-bottom: 14px
        }

        .cta-box p {
            color: #ede9ff;
            margin-bottom: 28px
        }

        .cta-actions {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap
        }

        .footer {
            max-width: 1180px;
            margin: auto;
            padding: 70px 5% 34px;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 45px
        }

        .footer-brand p {
            color: #64748b;
            line-height: 1.7;
            margin-top: 15px
        }

        .footer-links {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px
        }

        .footer-links h4 {
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 14px
        }

        .footer-links a {
            display: block;
            color: #475569;
            margin-bottom: 10px;
            font-size: 14px
        }

        .footer-bottom {
            grid-column: 1/-1;
            border-top: 1px solid #e2e8f0;
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
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: #f2efff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5337ec
        }

        @media(max-width:980px) {
            .hero-shell {
                grid-template-columns: 1fr
            }

            .feature-grid,
            .pricing-grid,
            .steps-grid,
            .stat-strip {
                grid-template-columns: 1fr 1fr
            }

            .pricing-card.popular {
                transform: none
            }

            .footer {
                grid-template-columns: 1fr
            }

            .nav-links {
                display: none
            }
        }

        @media(max-width:640px) {
            nav {
                top: 8px;
                width: 94%;
                height: auto;
                padding: 12px
            }

            .nav-cta .btn-ghost {
                display: none
            }

            .hero {
                padding-top: 120px
            }

            .hero-shell,
            .feature-grid,
            .pricing-grid,
            .steps-grid,
            .stat-strip {
                grid-template-columns: 1fr
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
    

/* =========================================================
   MOBILE RESPONSIVE FIXES - Added revision
   ========================================================= */
@media (max-width: 768px) {
    body { overflow-x: hidden; }

    nav {
        top: 8px;
        left: 12px;
        right: 12px;
        transform: none;
        width: auto;
        height: auto;
        min-height: 62px;
        padding: 10px 12px;
        border-radius: 18px;
        gap: 10px;
        background: rgba(255,255,255,.94);
    }

    .nav-logo img,
    .footer-brand img {
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
        padding: 10px 12px;
        height: auto;
        font-size: 11.5px;
        white-space: nowrap;
    }

    .hero {
        min-height: auto;
        padding: 112px 18px 54px;
    }

    .hero:after {
        right: -42%;
        top: 70px;
        width: 92vw;
        height: 92vw;
        opacity: .55;
    }

    .hero-shell {
        grid-template-columns: 1fr !important;
        gap: 28px;
    }

    .hero-badge {
        max-width: 100%;
        font-size: 11px;
        line-height: 1.45;
        padding: 8px 12px;
        margin-bottom: 16px;
    }

    h1,
    .hero h1 {
        font-size: clamp(31px, 10vw, 42px);
        line-height: 1.1;
        letter-spacing: -1.1px;
        margin-bottom: 16px;
    }

    h1 br,
    .hero h1 br { display: none; }

    .hero p,
    .section-desc {
        font-size: 14.5px;
        line-height: 1.7;
    }

    .hero p { margin-bottom: 24px; }

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
        margin-left: -8px;
    }

    .trust-text {
        font-size: 12.5px;
        line-height: 1.5;
    }

    .hero-visual {
        border-radius: 24px;
        padding: 12px;
    }

    .dashboard {
        padding: 16px;
        border-radius: 20px;
    }

    .dash-top {
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .class-card {
        padding: 18px;
        border-radius: 18px;
    }

    .class-card h3 {
        font-size: 20px;
        line-height: 1.25;
    }

    .mini-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .mini-card {
        border-radius: 16px;
        padding: 14px;
    }

    .stat-strip,
    .stats-bar {
        margin: 0 18px 42px;
        padding: 16px;
        border-radius: 22px;
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

    .feature-grid,
    .pricing-grid,
    .steps-grid {
        grid-template-columns: 1fr !important;
        grid-auto-rows: auto;
        gap: 14px;
    }

    .feature-card,
    .feature-card:first-child {
        grid-row: auto;
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

    .steps-section { padding-top: 56px; padding-bottom: 56px; }

    .step-item {
        padding: 20px;
        border-radius: 20px;
    }

    .step-number {
        font-size: 34px;
        margin-bottom: 8px;
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
    }

    .cta-box {
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
    nav { left: 10px; right: 10px; }
    .nav-logo img { width: 108px !important; }
    .btn-primary { padding: 9px 10px; font-size: 11px; }
    .hero,
    .section,
    .steps-section,
    .pricing-section,
    .testi-section,
    .cta-section { padding-left: 14px; padding-right: 14px; }
    .stat-strip,
    .stats-bar { margin-left: 14px; margin-right: 14px; }
}

</style>
</head>

<body>
    <nav id="navbar"><a href="{{ url('/') }}" class="nav-logo"><img src="{{ asset('images/logo2.png') }}" alt="Puwinter"></a>
        <ul class="nav-links">
            <li><a href="#fitur">Fitur</a></li>
            <li><a href="#cara-kerja">Cara Kerja</a></li>
            <li><a href="#harga">Harga</a></li>
            <li><a href="#testimoni">Testimoni</a></li>
        </ul>
        <div class="nav-cta"><a href="{{ route('login') }}" class="btn-ghost">Masuk</a><a href="{{ route('register') }}" class="btn-primary">Daftar Gratis</a></div>
    </nav>
    @php
    $displayStudents = $stats['total_students'] >= 1000
    ? number_format($stats['total_students'] / 1000, 0) . 'K+'
    : $stats['total_students'] . '+';
    $displaySoal = $stats['total_soal'] >= 1000
    ? number_format($stats['total_soal'] / 1000, 1) . 'K+'
    : $stats['total_soal'] . '+';
    $displayMateri = $stats['total_materi'] . '+';
    $displayKelas = $stats['total_kelas'] . '+';
    @endphp

    <section class="hero">
        <div class="hero-shell">
            <div class="hero-content">
                <div class="hero-badge"><span class="dot"></span>The #1 English Online Tutoring Platform in Indonesia</div>
                <h1>Master English with Indonesia’s <br> <span class="highlight">#1 Online Tutoring Platform</span></h1>
                <p>We guide students in mastering English based on their individual learning needs.</p>
                <div class="hero-actions"><a href="{{ route('register') }}" class="btn-hero">Mulai Belajar Gratis <i class="fas fa-arrow-right"></i></a><a href="#fitur" class="btn-hero-ghost"><i class="fas fa-play-circle"></i> Lihat Fitur</a></div>
                <div class="hero-trust">
                    <div class="trust-avatars"><span>A</span><span>R</span><span>D</span><span>N</span><span style="background:#7C3AED;">+</span></div>
                    <div class="trust-text">Bergabung dengan Puwinter</div>
                </div>
            </div>
            <div class="hero-visual">
                <div class="dashboard">
                    <div class="dash-top"><strong>Live Class Berlangsung</strong><span class="dash-pill">LIVE</span></div>
                    <div class="class-card">
                        <div>142 peserta online</div>
                        <h3>English Grammar — Advanced Sentence Structure</h3>
                        <p>Progress Belajar Minggu Ini</p>
                    </div>
                    <div class="mini-grid">
                        <div class="mini-card"><span>Peringkat</span><br><strong>128</strong>
                            <p>dari {{ number_format($stats['total_students']) }} peserta</p>
                        </div>
                        <div class="mini-card"><span>Pencapaian Baru!</span><br><strong>+36%</strong>
                            <p>Rising Star 🌟</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="stat-strip">
        <div class="stat-item reveal">
            <div class="stat-number">{{ $displayStudents }}</div>
            <div class="stat-desc">Sedang bergabung</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">{{ $displaySoal }}</div>
            <div class="stat-desc">English exercises + explanations</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">{{ $displayMateri }}</div>
            <div class="stat-desc">Materi premium</div>
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
            ['icon'=>'fa-video','color'=>'rgba(83,55,236,0.15)','icolor'=>'#8b7dff','title'=>'Live Class Interaktif','desc'=>'Belajar langsung bersama mentor terbaik via Zoom. Tanya jawab real-time dan rekaman tersedia setelahnya.'],
            ['icon'=>'fa-bullseye','color'=>'rgba(239,68,68,0.15)','icolor'=>'#F87171','title'=>'English Proficiency Assessment','desc'=>'Simulasi English assessment dengan timer, navigasi soal, dan hasil skor instan setelah submit.'],
            ['icon'=>'fa-lightbulb','color'=>'rgba(245,158,11,0.15)','icolor'=>'#FBBF24','title'=>'Complete Learning Review','desc'=>'Setiap latihan dilengkapi pembahasan teks dan video tutor. Pahami grammar, vocabulary, dan context dengan lebih jelas.'],
            ['icon'=>'fa-file-pdf','color'=>'rgba(16,185,129,0.15)','icolor'=>'#34D399','title'=>'Premium English Materials','desc'=>'245+ dokumen materi English yang ringkas dan terstruktur. Download dan belajar kapan saja, di mana saja.'],
            ['icon'=>'fa-chart-bar','color'=>'rgba(124,58,237,0.15)','icolor'=>'#9b8cff','title'=>'Leaderboard & Ranking','desc'=>'Pantau posisimu di antara ribuan English learners lain. Filter per level, sekolah, kota, atau provinsi.'],
            ['icon'=>'fa-chart-line','color'=>'rgba(124,108,255,0.15)','icolor'=>'#7c6cff','title'=>'Analisis Belajar Detail','desc'=>'Lihat grafik progres dan distribusi waktu belajar secara visual untuk memantau perkembangan dan konsistensi belajar siswa.'],
            ] as $f)<div class="feature-card reveal">
                <div class="feature-icon" style="background:{{ $f['color'] }};"><i class="fas {{ $f['icon'] }}" style="color:{{ $f['icolor'] }};"></i></div>
                <h3>{{ $f['title'] }}</h3>
                <p>{{ $f['desc'] }}</p>
            </div>@endforeach</div>
    </div>
    <div id="cara-kerja" class="steps-section">
        <div class="steps-inner">
            <div class="section-label">Cara Kerja</div>
            <h2 class="section-title">Mulai dalam <span class="highlight">4 langkah mudah</span></h2>
            <div class="steps-grid">@foreach([['n'=>'1','title'=>'Daftar Gratis','desc'=>'Buat akun dalam 30 detik. Tidak perlu kartu kredit.'],['n'=>'2','title'=>'Pilih Materi','desc'=>'Akses ratusan materi dan latihan English sesuai kebutuhan belajarmu.'],['n'=>'3','title'=>'Ikuti Live Class','desc'=>'Bergabung ke live class dan tanya langsung ke mentor.'],['n'=>'4','title'=>'Pantau Progress','desc'=>'Cek leaderboard dan analisis belajarmu secara berkala.']] as $s)<div class="step-item reveal">
                    <div class="step-number">{{ $s['n'] }}</div>
                    <h3>{{ $s['title'] }}</h3>
                    <p>{{ $s['desc'] }}</p>
                </div>@endforeach</div>
        </div>
    </div>
    <div id="harga" class="pricing-section">
        <div style="text-align:center">
            <div class="section-label">Harga</div>
            <h2 class="section-title">Investasi terbaik untuk <span class="highlight">kemampuan English-mu</span></h2>
            <p class="section-desc" style="margin:auto">Mulai gratis, upgrade kapan saja. Garansi uang kembali 7 hari.</p>
        </div>
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
        <div style="text-align:center;margin-top:34px;color:#64748B;font-size:13.5px" class="reveal"><i class="fas fa-shield-halved" style="color:#10B981;"></i> Garansi uang kembali 7 hari — tidak puas, kami kembalikan 100%.</div>
    </div>
    <div id="testimoni" class="testi-section">
        <div class="testi-inner">
            <div style="margin-bottom:36px">
                <div class="section-label">Testimoni</div>
                <h2 class="section-title">Kata mereka yang sudah <span class="highlight">berhasil</span></h2>
            </div>
            <div class="testi-grid">@foreach([
                ['initial'=>'A','name'=>'Aditya Pratama','info'=>'English Speaking Program Student','color'=>'#5337ec','text'=>'"Puwinter benar-benar game changer. Live class-nya interaktif banget, bisa langsung tanya kalau ada yang gak ngerti. Speaking dan writing saya meningkat pesat dalam 2 bulan!"'],
                ['initial'=>'S','name'=>'Siti Rahayu','info'=>'TOEFL Preparation Student','color'=>'#7C3AED','text'=>'"Awalnya ragu karena harganya murah, tapi kualitasnya melebihi ekspektasi. Materinya detail dan tutor-nya sabar banget neranginnya."'],
                ['initial'=>'R','name'=>'Rafi Ahmad','info'=>'IELTS Preparation Student','color'=>'#059669','text'=>'"Yang bikin beda dari platform lain adalah analisis belajarnya. Saya tau persis bagian mana yang masih lemah dan harus diperkuat. Highly recommended!"'],
                ['initial'=>'N','name'=>'Nadia Putri','info'=>'Academic English Student','color'=>'#DC2626','text'=>'"Fitur leaderboard-nya bikin semangat belajar. Seru aja ngeliat nama sendiri naik terus. Plus materi English PDF-nya lengkap dan mudah dipahami."'],
                ['initial'=>'D','name'=>'Dimas Kurniawan','info'=>'Business English Student','color'=>'#6d5dfc','text'=>'"Mock test-nya sangat membantu untuk persiapan TOEFL dan IELTS. Saya jadi lebih terbiasa dengan format soal dan manajemen waktu."'],
                ['initial'=>'F','name'=>'Farah Nabila','info'=>'English Conversation Student','color'=>'#D97706','text'=>'"Support tim-nya responsif dan ramah. Pernah ada masalah teknis, langsung dibantu dalam hitungan menit. Pengalaman belajarnya nyaman banget."'],
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
    </div>
    <div class="cta-section">
        <div class="cta-box reveal">
            <h2>Ready to Master English?</h2>
            <p>Daftar gratis sekarang dan mulai belajar bersama {{ number_format($stats['total_students']) }}+ English learners lainnya.</p>
            <div class="cta-actions"><a href="{{ route('register') }}" class="btn-hero">Daftar Gratis Sekarang <i class="fas fa-arrow-right"></i></a><a href="{{ route('login') }}" class="btn-hero-ghost">Sudah punya akun? Masuk</a></div>
        </div>
    </div>
    <footer class="footer">
        <div class="footer-brand">
            <a href="{{ url('/') }}"><img src="{{ asset('images/logo2.png') }}" alt="Puwinter"></a>
            <p>Platform pembelajaran Bahasa Inggris terlengkap dan terpercaya di Indonesia.</p>
        </div>
        <div class="footer-links">
            <div>
                <h4>Platform</h4><a href="#">Live Class</a><a href="#">English Assessment</a><a href="#">Practice Bank</a><a href="#">English Materials</a><a href="#">Leaderboard</a>
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