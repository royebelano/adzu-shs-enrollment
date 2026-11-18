
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Senior High School  | Ateneo de Zamboanga University</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #101b35;
            --navy-light: #172849;
            --blue: #173d7a;
            --gold: #ffcc35;
            --gold-dark: #e8b51b;
            --text: #17233d;
            --muted: #68758c;
            --line: #dce3ee;
            --paper: #f5f7fa;
            --white: #fff;
            --serif: 'Playfair Display', Georgia, serif;
            --sans: 'DM Sans', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 85px;
        }

        body {
            font-family: var(--sans);
            color: var(--text);
            background: var(--paper);
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button {
            font-family: inherit;
        }

        ::selection {
            background: var(--gold);
            color: var(--navy);
        }

        /* SCROLL PROGRESS */

        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--gold);
            z-index: 9999;
        }

        /* HEADER */

        .site-header {
            height: 76px;
            padding: 0 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255,255,255,.97);
            border-bottom: 1px solid #e8edf4;
            transition: all .35s ease;
        }

        .site-header.scrolled {
            height: 66px;
            box-shadow: 0 8px 30px rgba(10,25,50,.10);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .brand-name {
            font-family: var(--serif);
            font-size: 16px;
            font-weight: 700;
            color: var(--navy);
        }

        .brand-subtitle {
            font-size: 9px;
            letter-spacing: 2px;
            font-weight: 800;
            color: #60718d;
            margin-top: 4px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 27px;
        }

        .nav-links a {
            font-size: 12px;
            font-weight: 700;
            color: var(--navy);
            position: relative;
        }

        .nav-links a:not(.nav-staff)::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -7px;
            width: 0;
            height: 2px;
            background: var(--gold-dark);
            transition: width .3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-staff {
            padding: 11px 16px;
            border: 1px solid #ccd6e6;
            border-radius: 7px;
            font-size: 9px !important;
            letter-spacing: 1px;
        }

        /* HERO */

        .hero {
            min-height: 620px;
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
            color: white;

            background:
                linear-gradient(
                    90deg,
                    rgba(7,14,31,.95) 0%,
                    rgba(7,14,31,.88) 38%,
                    rgba(7,14,31,.70) 70%,
                    rgba(7,14,31,.52) 100%
                ),
                url('/adzu.png');

            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(
                0deg,
                rgba(7,14,31,.35),
                transparent 35%
            );
        }

        .hero-container {
            width: min(1120px, 88%);
            margin: auto;
            padding: 90px 0;
            display: grid;
            grid-template-columns: 1.3fr .8fr;
            gap: 65px;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .hero-copy {
            animation: heroEntrance 1s cubic-bezier(.2,.7,.2,1) both;
        }

        @keyframes heroEntrance {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 9px 15px;
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 30px;
            background: rgba(255,255,255,.10);
            backdrop-filter: blur(10px);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #f1f3f8;
            margin-bottom: 25px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: var(--gold);
            border-radius: 50%;
            animation: statusPulse 2s infinite;
        }

        @keyframes statusPulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255,204,53,.5);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(255,204,53,0);
            }
        }

        .hero h1 {
            font-family: var(--serif);
            font-size: clamp(43px,5vw,67px);
            line-height: 1.08;
            letter-spacing: -1.8px;
            margin-bottom: 23px;
        }

        .hero h1 span {
            display: block;
            color: var(--gold);
            font-style: italic;
        }

        .hero-description {
            max-width: 530px;
            font-size: 14px;
            line-height: 1.9;
            color: #d6deec;
            margin-bottom: 30px;
        }

        /* BUTTONS */

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 13px;
        }

        .btn {
            position: relative;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 15px 22px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 800;
            overflow: hidden;
            isolation: isolate;
            transition: transform .3s ease, background .3s ease;
        }

        .btn:hover {
            transform: translateY(-4px);
        }

        .btn-primary {
            background: var(--gold);
            color: var(--navy);
        }

        .btn-primary:hover {
            background: #ffdf79;
        }

        .btn-outline {
            border: 1px solid rgba(255,255,255,.45);
            background: rgba(255,255,255,.09);
            color: white;
            backdrop-filter: blur(8px);
        }

        .btn-outline:hover {
            background: rgba(255,255,255,.19);
        }

        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.55);
            transform: scale(0);
            animation: rippleEffect .65s ease-out;
            pointer-events: none;
            z-index: -1;
        }

        @keyframes rippleEffect {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* DOCUMENT CHECKLIST */

        .hero-checklist {
            padding: 15px 0 15px 27px;
            border-left: 1px solid rgba(255,255,255,.3);
            animation: checklistEntrance 1.1s .2s both;
            transition: transform .4s ease;
        }

        @keyframes checklistEntrance {
            from {
                opacity: 0;
                transform: translateX(35px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-checklist:hover {
            transform: translateY(-5px);
        }

        .eyebrow {
            color: var(--gold);
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 2.5px;
            margin-bottom: 13px;
        }

        .hero-checklist h2 {
            font-family: var(--serif);
            font-size: 24px;
            margin-bottom: 22px;
        }

        .check-list {
            list-style: none;
        }

        .check-list li {
            display: flex;
            align-items: flex-start;
            gap: 11px;
            color: #e1e7f1;
            font-size: 12px;
            line-height: 1.65;
            margin-bottom: 13px;
            transition: transform .3s ease, color .3s ease;
        }

        .check-list li:hover {
            transform: translateX(5px);
            color: white;
        }

        .check-icon {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--gold);
            color: var(--gold);
            border-radius: 50%;
            font-size: 10px;
        }

        /* GENERAL SECTIONS */

        .section {
            padding: 100px 6%;
        }

        .section-inner {
            max-width: 1120px;
            margin: auto;
        }

        .section-label {
            color: #234c91;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 3px;
        }

        .section-title {
            font-family: var(--serif);
            font-size: clamp(31px,4vw,43px);
            color: var(--navy);
            line-height: 1.25;
            margin: 12px 0;
        }

        .section-description {
            max-width: 570px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.9;
        }

        /* ENROLLMENT GUIDE */

        .guide {
            background: #f4f7fb;
        }

        .guide-heading {
            display: grid;
            grid-template-columns: .75fr 1.25fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .guide-row {
            display: grid;
            grid-template-columns: 75px 1fr 1.55fr;
            align-items: start;
            gap: 28px;
            padding: 40px 0;
            border-top: 1px solid #cbd6e5;
        }

        .guide-number {
            font-family: var(--serif);
            font-size: 30px;
            font-style: italic;
            color: #9db3d0;
            transition: .4s ease;
        }

        .guide-row:hover .guide-number {
            color: var(--gold-dark);
            transform: translateX(5px);
        }

        .guide-category {
            color: #244c8e;
            font-size: 9px;
            letter-spacing: 2px;
            font-weight: 800;
            margin-bottom: 11px;
        }

        .guide-name {
            font-family: var(--serif);
            font-size: 19px;
            line-height: 1.4;
            color: var(--navy);
        }

        .guide-content p {
            color: #596c88;
            font-size: 13px;
            line-height: 1.9;
            margin-bottom: 16px;
        }

        .guide-note {
            padding: 14px 16px;
            background: white;
            border: 1px solid #e1e8f1;
            border-radius: 10px;
            color: #60718b;
            font-size: 11px;
            line-height: 1.8;
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .guide-row:hover .guide-note {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(20,40,70,.06);
        }

        .guide-note strong {
            color: var(--navy);
        }

        .note-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            background: var(--gold-dark);
            border-radius: 50%;
            margin-right: 8px;
        }

        /* REQUIREMENTS */

        .requirements {
            background: white;
        }

        .requirements-heading {
            text-align: center;
            margin-bottom: 45px;
        }

        .requirements-heading .section-description {
            margin: 12px auto 0;
        }

        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(2,1fr);
            gap: 14px;
        }

        .requirement-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 21px;
            background: #f8f9fc;
            border: 1px solid #e8edf4;
            border-radius: 12px;
            transition: transform .4s ease, box-shadow .4s ease, border-color .4s ease;
        }

        .requirement-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 15px 35px rgba(12,30,60,.10);
            border-color: #e4c45e;
        }

        .requirement-icon {
            width: 44px;
            height: 44px;
            flex-shrink: 0;
            border-radius: 11px;
            background: #edf2fa;
            color: #244c8e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            transition: transform .4s ease;
        }

        .requirement-card:hover .requirement-icon {
            transform: rotate(-8deg) scale(1.08);
        }

        .requirement-name {
            font-size: 12px;
            font-weight: 800;
            color: var(--navy);
            line-height: 1.5;
        }

        .requirement-type {
            color: #8b96a8;
            font-size: 10px;
            margin-top: 5px;
        }

        .requirements-info {
            margin-top: 24px;
            padding: 17px 20px;
            background: #fff9e8;
            border: 1px solid #f2e2ae;
            border-radius: 10px;
            color: #77612a;
            font-size: 11px;
            line-height: 1.8;
        }

        /* JOURNEY */

        .journey {
            background: var(--navy);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .journey::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            border: 1px solid rgba(255,255,255,.05);
            border-radius: 50%;
            top: -200px;
            right: -100px;
            box-shadow:
                0 0 0 50px rgba(255,255,255,.015),
                0 0 0 100px rgba(255,255,255,.01);
        }

        .journey .section-label {
            color: var(--gold);
        }

        .journey .section-title {
            color: white;
        }

        .journey .section-description {
            color: #b8c5d9;
        }

        .journey-grid {
            display: grid;
            grid-template-columns: repeat(3,1fr);
            gap: 18px;
            margin-top: 42px;
            position: relative;
            z-index: 1;
        }

        .journey-card {
            padding: 28px;
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 13px;
            background: rgba(255,255,255,.045);
            transition: transform .4s ease, background .4s ease, border-color .4s ease;
        }

        .journey-card:hover {
            transform: translateY(-8px);
            background: rgba(255,255,255,.09);
            border-color: rgba(255,204,53,.5);
        }

        .journey-card-number {
            font-family: var(--serif);
            font-size: 32px;
            font-style: italic;
            color: var(--gold);
            margin-bottom: 15px;
        }

        .journey-card h3 {
            font-family: var(--serif);
            font-size: 19px;
            margin-bottom: 11px;
        }

        .journey-card p {
            color: #b8c5d9;
            font-size: 12px;
            line-height: 1.8;
        }

        /* CTA */

        .cta {
            background: #f4f7fb;
            padding: 80px 6%;
        }

        .cta-inner {
            max-width: 1120px;
            margin: auto;
            padding: 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 30px;
            background: white;
            border: 1px solid #e4eaf2;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(20,40,70,.04);
        }

        .cta h2 {
            font-family: var(--serif);
            font-size: 30px;
            color: var(--navy);
            margin: 12px 0;
        }

        .cta p {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.8;
        }

        /* FOOTER */

        .site-footer {
            background: #0b1428;
            color: white;
            padding: 35px 6%;
        }

        .footer-inner {
            max-width: 1120px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .footer-school {
            font-family: var(--serif);
            font-size: 15px;
            font-weight: 700;
        }

        .footer-sub {
            color: #9ba9bd;
            font-size: 10px;
            margin-top: 6px;
        }

        .footer-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #c4cede;
            font-size: 11px;
            transition: color .2s;
        }

        .footer-links a:hover {
            color: var(--gold);
        }

        .footer-bottom {
            max-width: 1120px;
            margin: 25px auto 0;
            padding-top: 18px;
            border-top: 1px solid rgba(255,255,255,.12);
            color: #8290a7;
            font-size: 10px;
        }

        /* SCROLL REVEAL */

        .js-enabled .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition:
                opacity .8s cubic-bezier(.2,.7,.2,1),
                transform .8s cubic-bezier(.2,.7,.2,1);
        }

        .js-enabled .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* RESPONSIVE */

        @media(max-width: 900px) {

            .hero-container {
                grid-template-columns: 1fr;
                gap: 45px;
            }

            .hero-checklist {
                max-width: 520px;
            }

            .guide-heading {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .guide-row {
                grid-template-columns: 55px 1fr;
                gap: 18px;
            }

            .guide-content {
                grid-column: 2;
            }

            .journey-grid {
                grid-template-columns: repeat(2,1fr);
            }
        }

        @media(max-width: 600px) {

            .site-header {
                height: 68px;
                padding: 0 4%;
            }

            .brand {
                gap: 8px;
            }

            .brand img {
                width: 38px;
                height: 38px;
            }

            .brand-name {
                font-size: 11px;
            }

            .brand-subtitle {
                font-size: 6px;
                letter-spacing: 1px;
            }

            .nav-links {
                gap: 10px;
            }

            .nav-links a {
                font-size: 9px;
            }

            .nav-staff {
                padding: 9px !important;
                font-size: 8px !important;
            }

            .hero {
                min-height: auto;
            }

            .hero-container {
                width: 88%;
                padding: 75px 0;
            }

            .hero h1 {
                font-size: 43px;
            }

            .hero-description {
                font-size: 12px;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .hero-checklist {
                padding-left: 18px;
            }

            .section {
                padding: 70px 6%;
            }

            .guide-row {
                grid-template-columns: 42px 1fr;
                gap: 12px;
                padding: 30px 0;
            }

            .guide-number {
                font-size: 24px;
            }

            .guide-name {
                font-size: 17px;
            }

            .requirements-grid {
                grid-template-columns: 1fr;
            }

            .journey-grid {
                grid-template-columns: 1fr;
            }

            .cta-inner {
                padding: 28px 22px;
                flex-direction: column;
                align-items: flex-start;
            }

            .cta h2 {
                font-size: 25px;
            }

            .footer-inner {
                align-items: flex-start;
                flex-direction: column;
            }

        }

        @media(prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
                scroll-behavior: auto !important;
            }

            .js-enabled .reveal {
                opacity: 1;
                transform: none;
            }
        }

    </style>
</head>

<body>

@php
    $schoolYear = \App\Models\Setting::schoolYear();
@endphp

<div class="scroll-progress" id="scrollProgress"></div>

<!-- HEADER -->

<header class="site-header" id="siteHeader">

    <a href="{{ url('/') }}" class="brand">

        <img src="{{ asset('logo_adzu.jpg') }}" alt="AdZU Logo">

        <div>
            <div class="brand-name">
                Ateneo de Zamboanga University
            </div>

            <div class="brand-subtitle">
                SENIOR HIGH SCHOOL
            </div>
        </div>

    </a>

    <nav class="nav-links">

        <a href="{{ url('/enrollment/track') }}">
            Track application
        </a>

        <a href="{{ url('/admin/login') }}" class="nav-staff">
            STAFF PORTAL
        </a>

    </nav>

</header>


<!-- HERO SECTION -->

<section class="hero" id="hero">

    <div class="hero-container">

        <div class="hero-copy">

            <div class="status-pill">

                <span class="status-dot"></span>

                SY 2026-2027 APPLICATIONS ARE OPEN

            </div>

            <h1>
                Your Ateneo journey
                <span>starts here.</span>
            </h1>

            <p class="hero-description">
                A clear, guided path from your first appointment
                to official enrollment at AdZU Senior High School.
                Begin your application and take the next step
                toward your academic journey.
            </p>

            <div class="hero-actions">

                <a href="{{ url('/enrollment/form') }}" class="btn btn-primary">
                    Book an appointment <span>→</span>
                </a>

                <a href="{{ url('/enrollment/track') }}" class="btn btn-outline">
                    Track my application
                </a>

            </div>

        </div>


        <!-- BEFORE YOU BEGIN -->

        <aside class="hero-checklist">

            <div class="eyebrow">
                BEFORE YOU BEGIN
            </div>

            <h2>Prepare your documents</h2>

            <ul class="check-list">

                <li>
                    <span class="check-icon">✓</span>
                    <span>Original Report Card</span>
                </li>

                <li>
                    <span class="check-icon">✓</span>
                    <span>Original Good Moral Certificate</span>
                </li>

                <li>
                    <span class="check-icon">✓</span>
                    <span>Clear copy of PSA birth certificate</span>
                </li>

                <li>
                    <span class="check-icon">✓</span>
                    <span>1" x 1" ID picture (any background)</span>
                </li>

                <li>
                    <span class="check-icon">✓</span>
                    <span>Photocopy of baby immunization book</span>
                </li>

                <li>
                    <span class="check-icon">✓</span>
                    <span>Photocopy of Covid vaccination card</span>
                </li>

                <li>
                    <span class="check-icon">✓</span>
                    <span>Valid contact number and email address</span>
                </li>

            </ul>

        </aside>

    </div>

</section>


<!-- ENROLLMENT GUIDE -->

<section class="section guide" id="enrollment-guide">

    <div class="section-inner">

        <div class="guide-heading reveal">

            <div class="section-label">
                THE ENROLLMENT GUIDE
            </div>

            <div>

                <h2 class="section-title">
                    Six stages. One simple path forward.
                </h2>

                <p class="section-description">
                    Know what to prepare, where to go, and what comes next.
                    Your application follows the six stages of the
                    AdZU Senior High School enrollment process.
                </p>

            </div>

        </div>


        @php
            $steps = [
                [
                    'category' => 'ONLINE APPOINTMENT',
                    'title' => 'Reserve your campus visit',
                    'description' => 'Choose an available appointment date and time slot. Complete your student information and submit your required documents.',
                    'note' => 'Check the available appointment schedules before proceeding.'
                ],
                [
                    'category' => 'HEALTH CLEARANCE',
                    'title' => 'Visit the university infirmary',
                    'description' => 'Proceed to the Infirmary for the required medical assessment, physical examination, and dental evaluation.',
                    'note' => 'Follow the instructions of the assigned medical personnel.'
                ],
                [
                    'category' => 'ADMISSIONS',
                    'title' => 'Complete your admission assessment',
                    'description' => 'The Admissions Office reviews your submitted student information and supporting documents.',
                    'note' => 'Ensure that your information and submitted documents are accurate.'
                ],
                [
                    'category' => 'ID CAPTURING',
                    'title' => 'Complete your student ID capturing',
                    'description' => 'Proceed to the designated office for your student identification capturing process.',
                    'note' => 'Follow the instructions of the assigned personnel.'
                ],
                [
                    'category' => 'GCO ASSESSMENT',
                    'title' => 'Complete your GCO assessment',
                    'description' => 'Proceed to the Guidance and Counseling Office for the required student assessment.',
                    'note' => 'Follow your assigned GCO assessment schedule.'
                ],
                [
                    'category' => 'OFFICIAL ENROLLMENT',
                    'title' => 'Welcome to AdZU Senior High School',
                    'description' => 'After completing all required enrollment stages and receiving approval, your application reaches its enrolled status.',
                    'note' => 'Continue tracking your application until the process is complete.'
                ],
            ];
        @endphp


        @foreach($steps as $index => $step)

            <article class="guide-row reveal">

                <div class="guide-number">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                </div>

                <div>

                    <div class="guide-category">
                        {{ $step['category'] }}
                    </div>

                    <h3 class="guide-name">
                        {{ $step['title'] }}
                    </h3>

                </div>

                <div class="guide-content">

                    <p>
                        {{ $step['description'] }}
                    </p>

                    <div class="guide-note">

                        <span class="note-dot"></span>

                        <strong>What to do:</strong>

                        {{ $step['note'] }}

                    </div>

                </div>

            </article>

        @endforeach

    </div>

</section>


<!-- REQUIREMENTS -->

<section class="section requirements" id="requirements">

    <div class="section-inner">

        <div class="requirements-heading reveal">

            <div class="section-label">
                ADMISSION CHECKLIST
            </div>

            <h2 class="section-title">
                Your documents matter.
            </h2>

            <p class="section-description">
                Prepare the following documents before starting
                your online enrollment application.
            </p>

        </div>


        @php
            $documents = [
                [
                    'icon' => '📄',
                    'name' => 'Original Report Card',
                    'type' => 'Required'
                ],
                [
                    'icon' => '📝',
                    'name' => 'Original Good Moral Certificate',
                    'type' => 'Required'
                ],
                [
                    'icon' => '📃',
                    'name' => 'Clear copy of PSA Birth Certificate',
                    'type' => 'Required'
                ],
                [
                    'icon' => '📷',
                    'name' => '1" x 1" ID Picture (Any Background)',
                    'type' => 'Required'
                ],
                [
                    'icon' => '💉',
                    'name' => 'Photocopy of Baby Immunization Book',
                    'type' => 'Required'
                ],
                [
                    'icon' => '🩹',
                    'name' => 'Photocopy of COVID Vaccination Card',
                    'type' => 'Required'
                ],
            ];
        @endphp


        <div class="requirements-grid">

            @foreach($documents as $document)

                <div class="requirement-card reveal">

                    <div class="requirement-icon">
                        {{ $document['icon'] }}
                    </div>

                    <div>

                        <div class="requirement-name">
                            {{ $document['name'] }}
                        </div>

                        <div class="requirement-type">
                            <strong style="color:#b17c00">
                                {{ strtoupper($document['type']) }}
                            </strong>
                        </div>

                    </div>

                </div>

            @endforeach

        </div>


        <div class="requirements-info reveal">

            <strong>Important:</strong>

            Please bring the original documents and prepare clear
            photocopies where required. Make sure all documents
            are complete before your appointment.

        </div>

    </div>

</section>


<!-- APPLICATION JOURNEY -->

<section class="section journey">

    <div class="section-inner">

        <div class="section-label reveal">
            YOUR APPLICATION
        </div>

        <h2 class="section-title reveal">
            Start with confidence.
        </h2>

        <p class="section-description reveal">
            Our enrollment process guides you from appointment
            scheduling to the completion of your application.
        </p>


        <div class="journey-grid">

            <div class="journey-card reveal">

                <div class="journey-card-number">01</div>

                <h3>Book your appointment</h3>

                <p>
                    Select an available schedule and complete
                    your student information.
                </p>

            </div>


            <div class="journey-card reveal">

                <div class="journey-card-number">02</div>

                <h3>Submit your requirements</h3>

                <p>
                    Prepare your documents and provide
                    the necessary application details.
                </p>

            </div>


            <div class="journey-card reveal">

                <div class="journey-card-number">03</div>

                <h3>Track your application</h3>

                <p>
                    Save your reference ID and monitor
                    your enrollment progress online.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- CALL TO ACTION -->

<section class="cta">

    <div class="cta-inner reveal">

        <div>

            <div class="section-label">
                YOUR FUTURE STARTS HERE
            </div>

            <h2>
                Ready to begin your Ateneo journey?
            </h2>

            <p>
                Start your application for School Year 2026-2027.
                Take your first step toward becoming an AdZU student.
            </p>

        </div>

        <div class="hero-actions">

            <a href="{{ url('/enrollment/form') }}" class="btn btn-primary">
                Book an appointment →
            </a>

        </div>

    </div>

</section>


<!-- FOOTER -->

<footer class="site-footer">

    <div class="footer-inner">

        <div>

            <div class="footer-school">
                Ateneo de Zamboanga University
            </div>

            <div class="footer-sub">
                Senior High School Admissions · SY 2026-2027
            </div>

        </div>

        <div class="footer-links">

            <a href="{{ url('/') }}">Home</a>

            <a href="#enrollment-guide">Enrollment Guide</a>

            <a href="#requirements">Requirements</a>

            <a href="{{ url('/enrollment/track') }}">Track Application</a>

            <a href="{{ url('/admin/login') }}">Staff Portal</a>

        </div>

    </div>

    <div class="footer-bottom">

        © {{ date('Y') }} Ateneo de Zamboanga University.
        Senior High School Admissions.

    </div>

</footer>


<!-- JAVASCRIPT ANIMATIONS -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    'use strict';

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)'
    ).matches;


    // ========================================
    // 1. SCROLL REVEAL
    // ========================================

    document.documentElement.classList.add('js-enabled');

    const revealElements = document.querySelectorAll('.reveal');

    if (reducedMotion || !('IntersectionObserver' in window)) {

        revealElements.forEach(element => {
            element.classList.add('active');
        });

    } else {

        const observer = new IntersectionObserver(
            (entries, observer) => {

                entries.forEach(entry => {

                    if (entry.isIntersecting) {

                        entry.target.classList.add('active');

                        observer.unobserve(entry.target);

                    }

                });

            },
            {
                threshold: 0.10,
                rootMargin: '0px 0px -35px 0px'
            }
        );

        revealElements.forEach(element => {
            observer.observe(element);
        });

    }


    // ========================================
    // 2. STAGGER ANIMATION
    // ========================================

    function stagger(selector, delay) {

        document.querySelectorAll(selector).forEach((element, index) => {

            element.style.transitionDelay = reducedMotion
                ? '0ms'
                : `${index * delay}ms`;

        });

    }

    stagger('.guide-row', 110);
    stagger('.requirement-card', 90);
    stagger('.journey-card', 140);


    // ========================================
    // 3. SCROLL PROGRESS
    // ========================================

    const progress = document.getElementById('scrollProgress');
    const header = document.getElementById('siteHeader');

    let ticking = false;

    function updateScroll() {

        if (ticking) return;

        ticking = true;

        requestAnimationFrame(() => {

            const scrollTop = window.scrollY;

            const scrollHeight =
                document.documentElement.scrollHeight - window.innerHeight;

            const percentage = scrollHeight > 0
                ? (scrollTop / scrollHeight) * 100
                : 0;

            progress.style.width = percentage + '%';

            header.classList.toggle('scrolled', scrollTop > 30);

            ticking = false;

        });

    }

    window.addEventListener('scroll', updateScroll, {
        passive: true
    });

    updateScroll();


    // ========================================
    // 4. HERO PARALLAX
    // ========================================

    const hero = document.getElementById('hero');

    const canHover = window.matchMedia(
        '(hover: hover) and (pointer: fine)'
    ).matches;

    if (hero && canHover && !reducedMotion) {

        let parallaxTicking = false;

        hero.addEventListener('mousemove', function (event) {

            if (parallaxTicking) return;

            parallaxTicking = true;

            requestAnimationFrame(() => {

                const rect = hero.getBoundingClientRect();

                const x = (event.clientX - rect.left) / rect.width;
                const y = (event.clientY - rect.top) / rect.height;

                const moveX = (x - .5) * 12;
                const moveY = (y - .5) * 12;

                hero.style.backgroundPosition =
                    `calc(50% + ${moveX}px) calc(50% + ${moveY}px)`;

                parallaxTicking = false;

            });

        });

        hero.addEventListener('mouseleave', function () {

            hero.style.backgroundPosition = 'center';

        });

    }


    // ========================================
    // 5. BUTTON RIPPLE
    // ========================================

    document.querySelectorAll('.btn').forEach(button => {

        button.addEventListener('click', function (event) {

            if (reducedMotion) return;

            const rect = button.getBoundingClientRect();

            const ripple = document.createElement('span');

            const size = Math.max(rect.width, rect.height);

            ripple.className = 'ripple';

            ripple.style.width = size + 'px';
            ripple.style.height = size + 'px';

            ripple.style.left =
                (event.clientX - rect.left - size / 2) + 'px';

            ripple.style.top =
                (event.clientY - rect.top - size / 2) + 'px';

            button.appendChild(ripple);

            ripple.addEventListener('animationend', function () {
                ripple.remove();
            });

        });

    });


    // ========================================
    // 6. SMOOTH ANCHOR NAVIGATION
    // ========================================

    document.querySelectorAll('a[href^="#"]').forEach(link => {

        link.addEventListener('click', function (event) {

            const targetId = link.getAttribute('href');

            if (!targetId || targetId === '#') return;

            const target = document.querySelector(targetId);

            if (!target) return;

            event.preventDefault();

            target.scrollIntoView({
                behavior: reducedMotion ? 'auto' : 'smooth',
                block: 'start'
            });

        });

    });


    // ========================================
    // 7. REQUIREMENT CARD HOVER
    // ========================================

    document.querySelectorAll('.requirement-card').forEach(card => {

        card.addEventListener('mouseenter', function () {

            card.style.borderColor = '#e4c45e';

        });

        card.addEventListener('mouseleave', function () {

            card.style.borderColor = '';

        });

    });


    // ========================================
    // 8. INITIALIZE
    // ========================================

    console.log(
        '%cAdZU Senior High School Admissions',
        'font-size:16px;font-weight:bold;color:#173d7a'
    );

    console.log('Premium animations initialized.');

});
</script>

</body>
</html>
