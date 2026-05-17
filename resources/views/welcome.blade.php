<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puwinter — Platform UTBK Terbaik Indonesia</title>
    <meta name="description" content="Belajar UTBK lebih cerdas bersama Puwinter. Live class, tryout, dan pembahasan soal bersama tutor terbaik.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary:   #2563EB;
            --primary-d: #1D4ED8;
            --dark:      #0A0F1E;
            --dark2:     #0F172A;
            --accent:    #38BDF8;
            --gold:      #F59E0B;
            --text:      #E2E8F0;
            --muted:     #64748B;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--dark);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ------------------------------------------------------------------ */
        /* NAVBAR                                                               */
        /* ------------------------------------------------------------------ */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 0 5%;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background 0.3s, backdrop-filter 0.3s;
        }

        nav.scrolled {
            background: rgba(10,15,30,0.95);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-logo-icon {
            width: 38px; height: 38px;
            background: var(--primary);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
        }

        .nav-logo-text {
            font-family: 'Sora', sans-serif;
            font-size: 17px;
            font-weight: 800;
            color: #fff;
        }

        .nav-logo-sub {
            font-size: 9px;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }

        .nav-links a {
            font-size: 13.5px;
            font-weight: 500;
            color: #94A3B8;
            text-decoration: none;
            transition: color 0.15s;
        }

        .nav-links a:hover { color: #fff; }

        .nav-cta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-ghost {
            padding: 8px 18px;
            border: 1.5px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            transition: all 0.15s;
            font-family: inherit;
        }

        .btn-ghost:hover {
            border-color: rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.05);
        }

        .btn-primary {
            padding: 9px 20px;
            background: var(--primary);
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            transition: background 0.15s, transform 0.1s;
            font-family: inherit;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover { background: var(--primary-d); transform: translateY(-1px); }

        /* ------------------------------------------------------------------ */
        /* HERO                                                                 */
        /* ------------------------------------------------------------------ */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 100px 5% 80px;
            position: relative;
            overflow: hidden;
        }

        /* Animated grid background */
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(37,99,235,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37,99,235,0.08) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
        }

        /* Glow orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }

        .orb-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(37,99,235,0.25) 0%, transparent 70%);
            top: -100px; left: -100px;
            animation: float1 8s ease-in-out infinite;
        }

        .orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(56,189,248,0.15) 0%, transparent 70%);
            bottom: 0; right: 10%;
            animation: float2 10s ease-in-out infinite;
        }

        .orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(124,58,237,0.15) 0%, transparent 70%);
            top: 30%; right: 20%;
            animation: float1 12s ease-in-out infinite reverse;
        }

        @keyframes float1 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, -30px); }
        }

        @keyframes float2 {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-20px, 20px); }
        }

        .hero-content {
            max-width: 680px;
            position: relative;
            z-index: 2;
            animation: fadeUp 0.8s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            background: rgba(37,99,235,0.15);
            border: 1px solid rgba(37,99,235,0.3);
            border-radius: 99px;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 24px;
            animation: fadeUp 0.8s 0.1s ease both;
        }

        .hero-badge .dot {
            width: 6px; height: 6px;
            background: var(--accent);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .hero h1 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(36px, 5vw, 62px);
            font-weight: 800;
            line-height: 1.12;
            color: #fff;
            margin-bottom: 22px;
            animation: fadeUp 0.8s 0.2s ease both;
        }

        .hero h1 .highlight {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 17px;
            color: #94A3B8;
            line-height: 1.75;
            max-width: 520px;
            margin-bottom: 36px;
            animation: fadeUp 0.8s 0.3s ease both;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            animation: fadeUp 0.8s 0.4s ease both;
        }

        .btn-hero {
            padding: 14px 28px;
            background: var(--primary);
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 8px 32px rgba(37,99,235,0.4);
        }

        .btn-hero:hover {
            background: var(--primary-d);
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(37,99,235,0.5);
        }

        .btn-hero-ghost {
            padding: 14px 28px;
            border: 1.5px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.15s;
        }

        .btn-hero-ghost:hover {
            border-color: rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.05);
        }

        .hero-trust {
            margin-top: 48px;
            display: flex;
            align-items: center;
            gap: 16px;
            animation: fadeUp 0.8s 0.5s ease both;
        }

        .trust-avatars {
            display: flex;
        }

        .trust-avatars span {
            width: 34px; height: 34px;
            border-radius: 50%;
            border: 2px solid var(--dark);
            background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-weight: 700;
            color: #fff;
            margin-left: -8px;
        }

        .trust-avatars span:first-child { margin-left: 0; }

        .trust-text { font-size: 13px; color: #64748B; }
        .trust-text strong { color: #fff; }

        /* Hero right — floating cards */
        .hero-visual {
            position: absolute;
            right: 5%;
            top: 50%;
            transform: translateY(-50%);
            width: 420px;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 14px;
            animation: fadeUp 0.8s 0.3s ease both;
        }

        .float-card {
            background: rgba(15,23,42,0.8);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 16px 20px;
            backdrop-filter: blur(12px);
            transition: transform 0.3s;
        }

        .float-card:hover { transform: translateY(-4px); }

        .float-card.card-1 { animation: floatCard1 6s ease-in-out infinite; }
        .float-card.card-2 { animation: floatCard2 7s ease-in-out infinite; margin-left: 40px; }
        .float-card.card-3 { animation: floatCard1 8s ease-in-out infinite reverse; }

        @keyframes floatCard1 {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes floatCard2 {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(8px); }
        }

        /* ------------------------------------------------------------------ */
        /* STATS BAR                                                            */
        /* ------------------------------------------------------------------ */
        .stats-bar {
            padding: 40px 5%;
            background: rgba(15,23,42,0.6);
            border-top: 1px solid rgba(255,255,255,0.06);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .stats-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            divide: rgba(255,255,255,0.08);
        }

        .stat-item {
            text-align: center;
            padding: 0 20px;
            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .stat-item:last-child { border-right: none; }

        .stat-number {
            font-family: 'Sora', sans-serif;
            font-size: 36px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-number span { color: var(--accent); }

        .stat-desc {
            font-size: 13px;
            color: #64748B;
        }

        /* ------------------------------------------------------------------ */
        /* FEATURES                                                             */
        /* ------------------------------------------------------------------ */
        .section {
            padding: 100px 5%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--accent);
            margin-bottom: 14px;
        }

        .section-label::before {
            content: '';
            width: 20px;
            height: 2px;
            background: var(--accent);
            border-radius: 99px;
        }

        .section-title {
            font-family: 'Sora', sans-serif;
            font-size: clamp(28px, 3.5vw, 42px);
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .section-title .hl { color: var(--accent); }

        .section-desc {
            font-size: 15px;
            color: #64748B;
            line-height: 1.75;
            max-width: 560px;
            margin-bottom: 56px;
        }

        /* Feature grid */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: rgba(15,23,42,0.6);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 16px;
            padding: 28px;
            transition: all 0.25s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            opacity: 0;
            transition: opacity 0.25s;
        }

        .feature-card:hover {
            border-color: rgba(37,99,235,0.3);
            transform: translateY(-6px);
            background: rgba(15,23,42,0.9);
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            margin-bottom: 18px;
        }

        .feature-card h3 {
            font-family: 'Sora', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
        }

        .feature-card p {
            font-size: 13.5px;
            color: #64748B;
            line-height: 1.7;
        }

        /* ------------------------------------------------------------------ */
        /* HOW IT WORKS                                                         */
        /* ------------------------------------------------------------------ */
        .steps-section {
            padding: 100px 5%;
            background: rgba(15,23,42,0.4);
        }

        .steps-inner {
            max-width: 1100px;
            margin: 0 auto;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            position: relative;
        }

        .steps-grid::before {
            content: '';
            position: absolute;
            top: 36px;
            left: 12%;
            right: 12%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(37,99,235,0.4), rgba(37,99,235,0.4), transparent);
        }

        .step-item {
            text-align: center;
            padding: 0 20px;
        }

        .step-number {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: rgba(37,99,235,0.1);
            border: 2px solid rgba(37,99,235,0.3);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-family: 'Sora', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--accent);
            position: relative;
            z-index: 1;
        }

        .step-item h3 {
            font-family: 'Sora', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 8px;
        }

        .step-item p {
            font-size: 13px;
            color: #64748B;
            line-height: 1.65;
        }

        /* ------------------------------------------------------------------ */
        /* PRICING                                                              */
        /* ------------------------------------------------------------------ */
        .pricing-section {
            padding: 100px 5%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            align-items: start;
        }

        .pricing-card {
            background: rgba(15,23,42,0.7);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 32px 28px;
            transition: transform 0.2s;
        }

        .pricing-card.popular {
            background: rgba(37,99,235,0.08);
            border-color: rgba(37,99,235,0.4);
            position: relative;
        }

        .pricing-card:hover { transform: translateY(-6px); }

        .popular-tag {
            position: absolute;
            top: -14px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--primary);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 16px;
            border-radius: 99px;
            white-space: nowrap;
            letter-spacing: 0.5px;
        }

        .pricing-name {
            font-size: 13px;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .pricing-price {
            font-family: 'Sora', sans-serif;
            font-size: 40px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 4px;
        }

        .pricing-price sup {
            font-size: 18px;
            font-weight: 600;
            vertical-align: super;
        }

        .pricing-period {
            font-size: 13px;
            color: #64748B;
            margin-bottom: 6px;
        }

        .pricing-strike {
            font-size: 13px;
            color: #475569;
            text-decoration: line-through;
            margin-bottom: 24px;
        }

        .pricing-features {
            list-style: none;
            margin-bottom: 28px;
        }

        .pricing-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            color: #94A3B8;
            padding: 7px 0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }

        .pricing-features li i {
            color: #10B981;
            font-size: 12px;
            flex-shrink: 0;
        }

        .btn-pricing {
            display: block;
            width: 100%;
            padding: 13px;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            transition: all 0.15s;
            font-family: inherit;
            cursor: pointer;
            border: none;
        }

        .btn-pricing-outline {
            border: 1.5px solid rgba(255,255,255,0.15);
            color: #fff;
            background: transparent;
        }

        .btn-pricing-outline:hover {
            border-color: var(--primary);
            color: var(--accent);
        }

        .btn-pricing-filled {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 6px 24px rgba(37,99,235,0.35);
        }

        .btn-pricing-filled:hover {
            background: var(--primary-d);
            transform: translateY(-1px);
        }

        /* ------------------------------------------------------------------ */
        /* TESTIMONIALS                                                         */
        /* ------------------------------------------------------------------ */
        .testi-section {
            padding: 100px 5%;
            background: rgba(15,23,42,0.4);
        }

        .testi-inner { max-width: 1100px; margin: 0 auto; }

        .testi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .testi-card {
            background: rgba(15,23,42,0.7);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 14px;
            padding: 24px;
            transition: transform 0.2s;
        }

        .testi-card:hover { transform: translateY(-4px); }

        .testi-stars {
            display: flex;
            gap: 3px;
            margin-bottom: 14px;
        }

        .testi-stars i { color: var(--gold); font-size: 13px; }

        .testi-text {
            font-size: 14px;
            color: #94A3B8;
            line-height: 1.75;
            margin-bottom: 18px;
            font-style: italic;
        }

        .testi-author {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .testi-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .testi-name {
            font-size: 13px;
            font-weight: 700;
            color: #fff;
        }

        .testi-info {
            font-size: 11.5px;
            color: #64748B;
            margin-top: 2px;
        }

        /* ------------------------------------------------------------------ */
        /* CTA BANNER                                                           */
        /* ------------------------------------------------------------------ */
        .cta-section {
            padding: 80px 5%;
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }

        .cta-box {
            background: linear-gradient(135deg, rgba(37,99,235,0.2) 0%, rgba(124,58,237,0.15) 100%);
            border: 1px solid rgba(37,99,235,0.3);
            border-radius: 24px;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -80px; left: 50%;
            transform: translateX(-50%);
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(37,99,235,0.2) 0%, transparent 70%);
            pointer-events: none;
        }

        .cta-box h2 {
            font-family: 'Sora', sans-serif;
            font-size: clamp(26px, 4vw, 40px);
            font-weight: 800;
            color: #fff;
            margin-bottom: 14px;
            position: relative;
            z-index: 1;
        }

        .cta-box p {
            font-size: 15px;
            color: #94A3B8;
            margin-bottom: 32px;
            position: relative;
            z-index: 1;
        }

        .cta-actions {
            display: flex;
            justify-content: center;
            gap: 14px;
            position: relative;
            z-index: 1;
            flex-wrap: wrap;
        }

        /* ------------------------------------------------------------------ */
        /* FOOTER                                                               */
        /* ------------------------------------------------------------------ */
        footer {
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 48px 5% 32px;
        }

        .footer-inner {
            max-width: 1100px;
            margin: 0 auto;
        }

        .footer-top {
            display: grid;
            grid-template-columns: 1.5fr repeat(3, 1fr);
            gap: 40px;
            margin-bottom: 48px;
        }

        .footer-brand p {
            font-size: 13px;
            color: #475569;
            line-height: 1.7;
            margin-top: 14px;
            max-width: 260px;
        }

        .footer-col h4 {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748B;
            margin-bottom: 16px;
        }

        .footer-col ul { list-style: none; }

        .footer-col ul li {
            margin-bottom: 10px;
        }

        .footer-col ul li a {
            font-size: 13.5px;
            color: #475569;
            text-decoration: none;
            transition: color 0.15s;
        }

        .footer-col ul li a:hover { color: #fff; }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.06);
            font-size: 12.5px;
            color: #334155;
        }

        .social-links {
            display: flex;
            gap: 12px;
        }

        .social-links a {
            width: 34px; height: 34px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #475569;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.15s;
        }

        .social-links a:hover {
            border-color: var(--primary);
            color: var(--accent);
        }

        /* Scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-visual { display: none; }
            .feature-grid { grid-template-columns: repeat(2, 1fr); }
            .pricing-grid { grid-template-columns: 1fr; max-width: 400px; margin: 0 auto; }
            .testi-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid { grid-template-columns: repeat(2, 1fr); }
            .steps-grid::before { display: none; }
            .footer-top { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 640px) {
            .nav-links { display: none; }
            .feature-grid { grid-template-columns: 1fr; }
            .testi-grid { grid-template-columns: 1fr; }
            .stats-inner { grid-template-columns: repeat(2, 1fr); gap: 24px; }
            .stat-item { border-right: none; }
            .footer-top { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- ======================================================================== --}}
{{-- NAVBAR                                                                     --}}
{{-- ======================================================================== --}}
<nav id="navbar">
    <a href="{{ url('/') }}" class="nav-logo">
        <div class="nav-logo-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" fill="#fff"/>
            </svg>
        </div>
        <div>
            <div class="nav-logo-text">Puwinter</div>
            <div class="nav-logo-sub">UTBK</div>
        </div>
    </a>

    <ul class="nav-links">
        <li><a href="#fitur">Fitur</a></li>
        <li><a href="#cara-kerja">Cara Kerja</a></li>
        <li><a href="#harga">Harga</a></li>
        <li><a href="#testimoni">Testimoni</a></li>
    </ul>

    <div class="nav-cta">
        <a href="{{ route('login') }}" class="btn-ghost">Masuk</a>
        <a href="{{ route('register') }}" class="btn-primary">Daftar Gratis</a>
    </div>
</nav>

{{-- ======================================================================== --}}
{{-- HERO                                                                       --}}
{{-- ======================================================================== --}}
<section class="hero">
    <div class="hero-grid"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="hero-content">
        <div class="hero-badge">
            <span class="dot"></span>
            Platform UTBK #1 di Indonesia
        </div>

        <h1>
            Belajar Lebih <span class="highlight">Cerdas</span>,<br>
            Lolos UTBK Lebih <span class="highlight">Pasti</span>
        </h1>

        <p>
            Platform persiapan UTBK terlengkap dengan live class interaktif, ribuan soal & pembahasan, tryout simulasi, dan analisis belajar personal bersama tutor terbaik.
        </p>

        <div class="hero-actions">
            <a href="{{ route('register') }}" class="btn-hero">
                Mulai Belajar Gratis <i class="fas fa-arrow-right"></i>
            </a>
            <a href="#fitur" class="btn-hero-ghost">
                <i class="fas fa-play-circle"></i> Lihat Fitur
            </a>
        </div>

        <div class="hero-trust">
            <div class="trust-avatars">
                <span>A</span><span>R</span><span>D</span><span>N</span><span style="background:#7C3AED;">+</span>
            </div>
            <div class="trust-text">
                Bergabung dengan <strong>24.560+</strong> pejuang UTBK aktif
            </div>
        </div>
    </div>

    {{-- Floating cards --}}
    <div class="hero-visual">
        <div class="float-card card-1">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <div style="width:36px; height:36px; background:rgba(37,99,235,0.2); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-video" style="color:#60A5FA;"></i>
                </div>
                <div>
                    <div style="font-size:12px; font-weight:700; color:#fff;">Live Class Berlangsung</div>
                    <div style="font-size:11px; color:#64748B;">142 peserta online</div>
                </div>
                <span style="background:#EF4444; color:#fff; font-size:9px; font-weight:700; padding:2px 7px; border-radius:99px; margin-left:auto;">LIVE</span>
            </div>
            <div style="font-size:13px; color:#94A3B8;">Matematika TPS — Limit Fungsi Aljabar</div>
        </div>

        <div class="float-card card-2">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <div style="font-size:12px; font-weight:700; color:#fff;">Progress Belajar Minggu Ini</div>
                <span style="font-size:18px; font-weight:800; color:#10B981;">+36%</span>
            </div>
            <div style="height:6px; background:rgba(255,255,255,0.08); border-radius:99px; overflow:hidden;">
                <div style="height:100%; width:78%; background:linear-gradient(90deg,#2563EB,#38BDF8); border-radius:99px;"></div>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:11px; color:#64748B; margin-top:6px;">
                <span>Peringkat: <strong style="color:#fff;">128</strong></span>
                <span>dari 24.560 peserta</span>
            </div>
        </div>

        <div class="float-card card-3">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:40px; height:40px; background:linear-gradient(135deg,#F59E0B,#EF4444); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-trophy" style="color:#fff;"></i>
                </div>
                <div>
                    <div style="font-size:12px; color:#64748B;">Pencapaian Baru!</div>
                    <div style="font-size:13px; font-weight:700; color:#fff;">Rising Star 🌟</div>
                    <div style="font-size:11px; color:#64748B;">Naik 100 peringkat hari ini</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ======================================================================== --}}
{{-- STATS BAR                                                                  --}}
{{-- ======================================================================== --}}
<div class="stats-bar">
    <div class="stats-inner">
        <div class="stat-item reveal">
            <div class="stat-number">24<span>K+</span></div>
            <div class="stat-desc">Pejuang UTBK aktif</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">12<span>K+</span></div>
            <div class="stat-desc">Soal + pembahasan</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">245<span>+</span></div>
            <div class="stat-desc">Materi premium</div>
        </div>
        <div class="stat-item reveal">
            <div class="stat-number">4.8<span>★</span></div>
            <div class="stat-desc">Rating rata-rata</div>
        </div>
    </div>
</div>

{{-- ======================================================================== --}}
{{-- FITUR                                                                      --}}
{{-- ======================================================================== --}}
<div id="fitur">
<div class="section">
    <div class="section-label">Fitur Unggulan</div>
    <h2 class="section-title">Semua yang kamu butuhkan<br>untuk <span class="hl">lolos UTBK</span></h2>
    <p class="section-desc">Dari live class interaktif hingga analisis belajar detail — semua tersedia dalam satu platform.</p>

    <div class="feature-grid">
        @foreach([
            ['icon'=>'fa-video','color'=>'rgba(37,99,235,0.15)','icolor'=>'#60A5FA','title'=>'Live Class Interaktif','desc'=>'Belajar langsung bersama mentor terbaik via Zoom. Tanya jawab real-time dan rekaman tersedia setelahnya.'],
            ['icon'=>'fa-bullseye','color'=>'rgba(239,68,68,0.15)','icolor'=>'#F87171','title'=>'Tryout UTBK Simulasi','desc'=>'Simulasi ujian persis seperti UTBK asli. Timer, navigasi soal, dan hasil skor instan setelah submit.'],
            ['icon'=>'fa-lightbulb','color'=>'rgba(245,158,11,0.15)','icolor'=>'#FBBF24','title'=>'Pembahasan Lengkap','desc'=>'Setiap soal ada pembahasan teks dan video tutor. Pahami konsep, bukan hanya hafal jawaban.'],
            ['icon'=>'fa-file-pdf','color'=>'rgba(16,185,129,0.15)','icolor'=>'#34D399','title'=>'Materi PDF Premium','desc'=>'245+ dokumen materi ringkas dan terstruktur. Download dan belajar kapan saja, di mana saja.'],
            ['icon'=>'fa-chart-bar','color'=>'rgba(124,58,237,0.15)','icolor'=>'#A78BFA','title'=>'Leaderboard & Ranking','desc'=>'Pantau posisimu di antara ribuan pejuang UTBK lain. Filter per sekolah, kota, atau provinsi.'],
            ['icon'=>'fa-chart-line','color'=>'rgba(56,189,248,0.15)','icolor'=>'#38BDF8','title'=>'Analisis Belajar Detail','desc'=>'Grafik progres, distribusi waktu belajar, dan rekomendasi materi personal berdasarkan hasil tryout.'],
        ] as $f)
        <div class="feature-card reveal">
            <div class="feature-icon" style="background:{{ $f['color'] }};">
                <i class="fas {{ $f['icon'] }}" style="color:{{ $f['icolor'] }};"></i>
            </div>
            <h3>{{ $f['title'] }}</h3>
            <p>{{ $f['desc'] }}</p>
        </div>
        @endforeach
    </div>
</div>
</div>

{{-- ======================================================================== --}}
{{-- CARA KERJA                                                                 --}}
{{-- ======================================================================== --}}
<div id="cara-kerja" class="steps-section">
    <div class="steps-inner">
        <div style="text-align:center; margin-bottom:56px;">
            <div class="section-label" style="justify-content:center;">Cara Kerja</div>
            <h2 class="section-title">Mulai dalam <span class="hl">4 langkah mudah</span></h2>
        </div>

        <div class="steps-grid">
            @foreach([
                ['n'=>'1','title'=>'Daftar Gratis','desc'=>'Buat akun dalam 30 detik. Tidak perlu kartu kredit.'],
                ['n'=>'2','title'=>'Pilih Materi','desc'=>'Akses ratusan materi dan soal sesuai kebutuhan belajarmu.'],
                ['n'=>'3','title'=>'Ikuti Live Class','desc'=>'Bergabung ke live class dan tanya langsung ke mentor.'],
                ['n'=>'4','title'=>'Pantau Progress','desc'=>'Cek leaderboard dan analisis belajarmu secara berkala.'],
            ] as $s)
            <div class="step-item reveal">
                <div class="step-number">{{ $s['n'] }}</div>
                <h3>{{ $s['title'] }}</h3>
                <p>{{ $s['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ======================================================================== --}}
{{-- HARGA                                                                      --}}
{{-- ======================================================================== --}}
<div id="harga" class="pricing-section">
    <div style="text-align:center; margin-bottom:56px;">
        <div class="section-label" style="justify-content:center;">Harga</div>
        <h2 class="section-title">Investasi terbaik untuk <span class="hl">masa depanmu</span></h2>
        <p class="section-desc" style="margin:14px auto 0; text-align:center;">Mulai gratis, upgrade kapan saja. Garansi uang kembali 7 hari.</p>
    </div>

    <div class="pricing-grid">
        {{-- Paket 1 Bulan --}}
        <div class="pricing-card reveal">
            <div class="pricing-name">Paket 1 Bulan</div>
            <div class="pricing-price"><sup>Rp</sup>89K</div>
            <div class="pricing-period">/ bulan</div>
            <div class="pricing-strike">Rp 129.000</div>
            <ul class="pricing-features">
                @foreach(['Akses semua kelas','Live class eksklusif','Tryout tanpa batas','Materi premium','Pembahasan video tutor','Analisis belajar detail','Tanpa iklan'] as $f)
                <li><i class="fas fa-check-circle"></i> {{ $f }}</li>
                @endforeach
            </ul>
            <a href="{{ route('register') }}" class="btn-pricing btn-pricing-outline">Mulai Sekarang</a>
        </div>

        {{-- Paket 6 Bulan (Popular) --}}
        <div class="pricing-card popular reveal">
            <div class="popular-tag">PALING POPULER</div>
            <div class="pricing-name">Paket 6 Bulan</div>
            <div class="pricing-price"><sup>Rp</sup>249K</div>
            <div class="pricing-period">/ 6 bulan</div>
            <div class="pricing-strike">Rp 498.000 · Hemat 50%</div>
            <ul class="pricing-features">
                @foreach(['Akses semua kelas','Live class eksklusif','Tryout tanpa batas','Materi premium','Pembahasan video tutor','Analisis belajar detail','Tanpa iklan','1x Konsultasi Personal'] as $f)
                <li><i class="fas fa-check-circle"></i> {{ $f }}</li>
                @endforeach
            </ul>
            <a href="{{ route('register') }}" class="btn-pricing btn-pricing-filled">Pilih Paket Ini</a>
        </div>

        {{-- Paket 12 Bulan --}}
        <div class="pricing-card reveal">
            <div class="pricing-name">Paket 12 Bulan</div>
            <div class="pricing-price"><sup>Rp</sup>399K</div>
            <div class="pricing-period">/ 12 bulan</div>
            <div class="pricing-strike">Rp 798.000 · Hemat 50%</div>
            <ul class="pricing-features">
                @foreach(['Akses semua kelas','Live class eksklusif','Tryout tanpa batas','Materi premium','Pembahasan video tutor','Analisis belajar detail','Tanpa iklan','2x Konsultasi Personal'] as $f)
                <li><i class="fas fa-check-circle"></i> {{ $f }}</li>
                @endforeach
            </ul>
            <a href="{{ route('register') }}" class="btn-pricing btn-pricing-outline">Mulai Sekarang</a>
        </div>
    </div>

    {{-- Garansi --}}
    <div style="text-align:center; margin-top:36px; display:flex; align-items:center; justify-content:center; gap:10px; color:#64748B; font-size:13.5px;" class="reveal">
        <i class="fas fa-shield-halved" style="color:#10B981;"></i>
        Garansi uang kembali 7 hari — tidak puas, kami kembalikan 100%.
    </div>
</div>

{{-- ======================================================================== --}}
{{-- TESTIMONI                                                                  --}}
{{-- ======================================================================== --}}
<div id="testimoni" class="testi-section">
    <div class="testi-inner">
        <div style="text-align:center; margin-bottom:56px;">
            <div class="section-label" style="justify-content:center;">Testimoni</div>
            <h2 class="section-title">Kata mereka yang sudah <span class="hl">berhasil</span></h2>
        </div>

        <div class="testi-grid">
            @foreach([
                ['initial'=>'A','name'=>'Aditya Pratama','info'=>'Diterima UI Teknik Informatika 2024','color'=>'#2563EB','text'=>'"Puwinter benar-benar game changer. Live class-nya interaktif banget, bisa langsung tanya kalau ada yang gak ngerti. Skor tryout saya naik 120 poin dalam 2 bulan!"'],
                ['initial'=>'S','name'=>'Siti Rahayu','info'=>'Diterima ITB Teknik Kimia 2024','color'=>'#7C3AED','text'=>'"Awalnya ragu karena harganya murah, tapi kualitasnya melebihi ekspektasi. Pembahasannya detail dan tutor-nya sabar banget neranginnya."'],
                ['initial'=>'R','name'=>'Rafi Ahmad','info'=>'Diterima Unpad Kedokteran 2024','color'=>'#059669','text'=>'"Yang bikin beda dari platform lain adalah analisis belajarnya. Saya tau persis bagian mana yang masih lemah dan harus diperkuat. Highly recommended!"'],
                ['initial'=>'N','name'=>'Nadia Putri','info'=>'Diterima UGM Akuntansi 2024','color'=>'#DC2626','text'=>'"Fitur leaderboard-nya bikin semangat belajar. Seru aja ngeliat nama sendiri naik terus. Plus materi PDF-nya lengkap dan mudah dipahami."'],
                ['initial'=>'D','name'=>'Dimas Kurniawan','info'=>'Diterima ITS Teknik Sipil 2024','color'=>'#0891B2','text'=>'"Tryout-nya mirip banget sama UTBK asli. Pas H-1 ujian gak kaget karena sudah terbiasa dengan format soal dan manajemen waktu."'],
                ['initial'=>'F','name'=>'Farah Nabila','info'=>'Diterima Unair Psikologi 2024','color'=>'#D97706','text'=>'"Support tim-nya responsif dan ramah. Pernah ada masalah teknis, langsung dibantu dalam hitungan menit. Pengalaman belajarnya nyaman banget."'],
            ] as $t)
            <div class="testi-card reveal">
                <div class="testi-stars">
                    @for($i=0;$i<5;$i++)<i class="fas fa-star"></i>@endfor
                </div>
                <p class="testi-text">{{ $t['text'] }}</p>
                <div class="testi-author">
                    <div class="testi-avatar" style="background:{{ $t['color'] }};">{{ $t['initial'] }}</div>
                    <div>
                        <div class="testi-name">{{ $t['name'] }}</div>
                        <div class="testi-info">{{ $t['info'] }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ======================================================================== --}}
{{-- CTA BANNER                                                                 --}}
{{-- ======================================================================== --}}
<div class="cta-section">
    <div class="cta-box reveal">
        <h2>Siap mulai perjalanan UTBK-mu?</h2>
        <p>Daftar gratis sekarang dan mulai belajar bersama 24.560+ pejuang UTBK lainnya.</p>
        <div class="cta-actions">
            <a href="{{ route('register') }}" class="btn-hero">
                Daftar Gratis Sekarang <i class="fas fa-arrow-right"></i>
            </a>
            <a href="{{ route('login') }}" class="btn-hero-ghost">
                Sudah punya akun? Masuk
            </a>
        </div>
    </div>
</div>

{{-- ======================================================================== --}}
{{-- FOOTER                                                                     --}}
{{-- ======================================================================== --}}
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="{{ url('/') }}" class="nav-logo" style="margin-bottom:14px; display:inline-flex;">
                    <div class="nav-logo-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" fill="#fff"/>
                        </svg>
                    </div>
                    <div style="margin-left:8px;">
                        <div class="nav-logo-text">Puwinter</div>
                        <div class="nav-logo-sub">UTBK</div>
                    </div>
                </a>
                <p>Platform persiapan UTBK terlengkap dan terpercaya. Belajar lebih cerdas, lolos lebih pasti.</p>
            </div>

            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="#">Live Class</a></li>
                    <li><a href="#">Tryout</a></li>
                    <li><a href="#">Bank Soal</a></li>
                    <li><a href="#">Materi PDF</a></li>
                    <li><a href="#">Leaderboard</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Perusahaan</h4>
                <ul>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Karir</a></li>
                    <li><a href="#">Hubungi Kami</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Kebijakan Refund</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <span>© {{ date('Y') }} Puwinter. All rights reserved.</span>
            <div class="social-links">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 20);
    });

    // Scroll reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, i * 80);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

</body>
</html>
