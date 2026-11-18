
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Track Application | AdZU Senior High School</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --navy: #14264f;
            --navy-dark: #0b1938;
            --green: #1b5e3b;
            --gold: #d9a441;
            --text: #202b3c;
            --muted: #758096;
            --line: #e8edf3;
            --surface: #ffffff;
            --background: #f4f7fb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 10% 10%, rgba(217,164,65,.10), transparent 28%),
                radial-gradient(circle at 90% 85%, rgba(20,38,79,.07), transparent 30%),
                var(--background);
        }

        a {
            text-decoration: none;
        }

        .topbar {
            height: 76px;
            background: rgba(255,255,255,.94);
            border-bottom: 1px solid rgba(20,38,79,.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(14px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--navy);
            font-weight: 800;
            letter-spacing: -.5px;
        }

        .brand img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .brand small {
            display: block;
            font-size: 10px;
            letter-spacing: 1.4px;
            color: var(--muted);
            margin-top: 3px;
        }

        .nav-link {
            color: var(--navy);
            font-size: 13px;
            font-weight: 700;
            padding: 11px 17px;
            border: 1px solid var(--line);
            border-radius: 9px;
            transition: .25s;
        }

        .nav-link:hover {
            background: var(--navy);
            color: white;
        }

        .page {
            width: min(1050px, calc(100% - 36px));
            margin: 55px auto 70px;
        }

        .intro {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 30px;
            animation: fadeUp .7s ease both;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 13px;
            border-radius: 30px;
            background: #fff5df;
            color: #94691e;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--gold);
        }

        h1 {
            color: var(--navy);
            font-size: clamp(29px, 5vw, 43px);
            letter-spacing: -1.8px;
            line-height: 1.15;
            margin: 17px 0 12px;
        }

        .intro p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
            margin: 0;
        }

        .main-card {
            background: var(--surface);
            border: 1px solid rgba(20,38,79,.07);
            border-radius: 20px;
            padding: 34px;
            box-shadow: 0 18px 55px rgba(22,39,75,.08);
            animation: fadeUp .8s ease both;
        }

        .form-heading {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 25px;
        }

        .form-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background: #edf2fb;
            color: var(--navy);
            font-size: 22px;
            flex: none;
        }

        .form-heading h2 {
            font-size: 17px;
            margin: 2px 0 5px;
            color: var(--navy);
        }

        .form-heading p {
            font-size: 12px;
            color: var(--muted);
            margin: 0;
            line-height: 1.6;
        }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 9px;
        }

        .field input {
            width: 100%;
            height: 49px;
            border: 1px solid #dfe5ee;
            border-radius: 9px;
            padding: 0 14px;
            font-family: inherit;
            font-size: 13px;
            color: var(--text);
            outline: none;
            background: #fbfcfe;
            transition: .2s;
        }

        .field input:focus {
            border-color: var(--navy);
            background: white;
            box-shadow: 0 0 0 4px rgba(20,38,79,.07);
        }

        .field input::placeholder {
            color: #aab2c0;
        }

        .submit-btn {
            width: 100%;
            height: 51px;
            border: none;
            border-radius: 9px;
            background: var(--navy);
            color: white;
            font-family: inherit;
            font-size: 13px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 23px;
            transition: .25s;
        }

        .submit-btn:hover {
            background: var(--green);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(27,94,59,.2);
        }

        .error {
            background: #fff0f0;
            color: #b42318;
            border: 1px solid #ffd7d7;
            padding: 13px 15px;
            border-radius: 9px;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .result {
            margin-top: 32px;
            padding-top: 30px;
            border-top: 1px solid var(--line);
            animation: fadeUp .6s ease both;
        }

        .result-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 22px;
        }

        .result-heading h2 {
            font-size: 19px;
            color: var(--navy);
            margin: 0 0 5px;
        }

        .result-heading p {
            font-size: 11px;
            color: var(--muted);
            margin: 0;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .info-item {
            padding: 15px;
            background: #f8fafd;
            border: 1px solid #edf0f5;
            border-radius: 10px;
            min-width: 0;
        }

        .info-item span {
            display: block;
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .7px;
            margin-bottom: 7px;
        }

        .info-item strong {
            display: block;
            color: var(--navy);
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .status-line {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 17px;
        }

        .status-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
        }

        .badge, .stage {
            display: inline-flex;
            align-items: center;
            padding: 7px 12px;
            border-radius: 30px;
            background: #fff5dc;
            color: #916511;
            font-size: 10px;
            font-weight: 800;
        }

        .badge.approved, .stage.enrolled {
            background: #e7f6ed;
            color: #187342;
        }

        .badge.rejected {
            background: #ffebeb;
            color: #b42318;
        }

        .badge.for_compliance {
            background: #e8f2ff;
            color: #1756a1;
        }

        .remarks {
            margin-top: 18px;
            padding: 16px;
            border-radius: 10px;
            background: #fff8e9;
            border: 1px solid #f6e4bb;
            color: #72551d;
            font-size: 12px;
            line-height: 1.7;
            white-space: pre-line;
        }

        .remarks strong {
            display: block;
            margin-bottom: 5px;
        }

        .timeline-section {
            margin-top: 35px;
        }

        .timeline-title {
            font-size: 16px;
            color: var(--navy);
            margin: 0 0 6px;
        }

        .timeline-subtitle {
            font-size: 11px;
            color: var(--muted);
            margin: 0 0 24px;
        }

        .timeline {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .timeline li {
            position: relative;
            display: flex;
            gap: 16px;
            padding-bottom: 24px;
        }

        .timeline li:last-child {
            padding-bottom: 0;
        }

        .timeline li::before {
            content: "";
            position: absolute;
            left: 15px;
            top: 32px;
            bottom: 0;
            width: 2px;
            background: #e4e9f0;
        }

        .timeline li:last-child::before {
            display: none;
        }

        .timeline li.done::before {
            background: var(--green);
        }

        .dot {
            position: relative;
            z-index: 1;
            flex: none;
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            border: 2px solid #dce2eb;
            background: white;
            color: #9aa4b5;
            font-size: 11px;
            font-weight: 800;
        }

        .timeline li.done .dot {
            background: var(--green);
            border-color: var(--green);
            color: white;
        }

        .timeline li.current .dot {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--navy-dark);
            animation: ring 2s ease-out infinite;
        }

        .timeline-content {
            padding-top: 2px;
        }

        .timeline strong {
            display: block;
            font-size: 13px;
            color: var(--navy);
            margin-bottom: 5px;
        }

        .timeline li:not(.done):not(.current) strong {
            color: #9aa4b5;
        }

        .timeline small {
            display: block;
            font-size: 11px;
            color: var(--muted);
            line-height: 1.7;
        }

        .timeline .when {
            color: var(--green);
            font-weight: 800;
            margin-top: 5px;
        }

        .back-link {
            display: flex;
            justify-content: center;
            margin-top: 25px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            transition: .2s;
        }

        .back-link:hover {
            color: var(--navy);
        }

        .footer {
            text-align: center;
            padding: 0 15px 30px;
            color: #929bad;
            font-size: 10px;
        }

        @keyframes ring {
            0% { box-shadow: 0 0 0 0 rgba(217,164,65,.45); }
            70% { box-shadow: 0 0 0 9px rgba(217,164,65,0); }
            100% { box-shadow: 0 0 0 0 rgba(217,164,65,0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 650px) {
            .topbar {
                height: 66px;
                padding: 0 18px;
            }

            .brand img {
                width: 38px;
                height: 38px;
            }

            .page {
                margin-top: 35px;
            }

            .main-card {
                padding: 23px 18px;
                border-radius: 15px;
            }

            .field-grid, .info-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .result-heading {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

<header class="topbar">
    <a href="{{ route('enrollment.create') }}" class="brand">
        <img src="{{ asset('logo_adzu.jpg') }}" alt="AdZU Logo">
        <div>
            ADZU
            <small>SENIOR HIGH SCHOOL</small>
        </div>
    </a>

    <a href="{{ url('/') }}" class="nav-link">
    ← Back to Home
</a>
</header>

<main class="page">

    <section class="intro">
        <span class="eyebrow">Enrollment Portal · SY 2026–2027</span>
        <h1>Track Your Application</h1>
        <p>
            Stay informed about your enrollment journey.
            Enter your Reference ID and registered email address
            to view your application status and progress.
        </p>
    </section>

    <section class="main-card">

        <div class="form-heading">
            <div class="form-icon">⌕</div>
            <div>
                <h2>Application Tracking</h2>
                <p>Enter the details associated with your submitted application.</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="error">
                <strong>Unable to find your application.</strong><br>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('enrollment.track.check') }}">
            @csrf

            <div class="field-grid">
                <div class="field">
                    <label for="tracking_number">REFERENCE ID</label>
                    <input
                        id="tracking_number"
                        name="tracking_number"
                        type="text"
                        placeholder="ADZU-2026-XXXXXX"
                        value="{{ old('tracking_number') }}"
                        required
                        style="text-transform:uppercase"
                    >
                </div>

                <div class="field">
                    <label for="email">REGISTERED EMAIL ADDRESS</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="you@example.com"
                        value="{{ old('email') }}"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="submit-btn">
                Check Application Status →
            </button>
        </form>

        @isset($enrollment)
            @php
                $current = $enrollment->stepNumber();

                $steps = [
                    1 => ['Enrollment form submitted', 'We received your enrollment form.'],
                    2 => ['Infirmary', 'Health examination record is prepared for the school doctor.'],
                    3 => ['Admission', 'Your documents and requirements are being checked.'],
                    4 => ['ID capturing', 'Your ID photo and ID are taken.'],
                    5 => ['GCO assessment', 'Assessment with the Guidance and Counseling Office.'],
                    6 => ['Enrolled', 'You are officially enrolled. Welcome!'],
                ];

                $schedule = [
                    4 => $enrollment->idCaptureLabel(),
                    5 => $enrollment->gcoLabel(),
                ];
            @endphp

            <div class="result">

                <div class="result-heading">
                    <div>
                        <h2>Application Details</h2>
                        <p>Here is the latest information about your enrollment.</p>
                    </div>

                    <span class="stage {{ $current === 6 ? 'enrolled' : '' }}">
                        {{ $enrollment->stageLabel() }}
                    </span>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <span>Reference ID</span>
                        <strong>{{ $enrollment->tracking_number }}</strong>
                    </div>

                    @if($enrollment->student_id)
                        <div class="info-item">
                            <span>Student ID Number</span>
                            <strong>{{ $enrollment->student_id }}</strong>
                        </div>
                    @endif

                    <div class="info-item">
                        <span>Student Name</span>
                        <strong>
                            {{ $enrollment->last_name }},
                            {{ $enrollment->first_name }}
                            {{ $enrollment->middle_name }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span>Date Submitted</span>
                        <strong>{{ $enrollment->created_at?->format('F d, Y h:i A') }}</strong>
                    </div>

                    @if($enrollment->appointmentLabel())
                        <div class="info-item">
                            <span>Admission Appointment</span>
                            <strong>{{ $enrollment->appointmentLabel() }}</strong>
                        </div>
                    @endif
                </div>

                <div class="status-line">
                    <span class="status-label">APPLICATION STATUS:</span>
                    <span class="badge {{ $enrollment->status }}">
                        {{ $enrollment->statusLabel() }}
                    </span>
                </div>

                @if($enrollment->remarks && in_array($enrollment->status, ['for_compliance', 'rejected'], true))
                    <div class="remarks">
                        <strong>Important Remarks</strong>
                        {{ $enrollment->remarks }}
                    </div>
                @endif

                <div class="timeline-section">
                    <h3 class="timeline-title">Your Enrollment Journey</h3>
                    <p class="timeline-subtitle">
                        Follow the progress of your application through each enrollment stage.
                    </p>

                    <ol class="timeline">
                        @foreach($steps as $n => [$title, $text])
                            @php
                                $done = $current === 6 || $n < $current;
                                $state = $done ? 'done' : ($n === $current ? 'current' : '');
                                $at = $enrollment->stepReachedAt($n);
                            @endphp

                            <li class="{{ $state }}">
                                <span class="dot">{{ $done ? '✓' : $n }}</span>

                                <div class="timeline-content">
                                    <strong>Step {{ $n }} · {{ $title }}</strong>

                                    <small>
                                        @if(($done || $n === $current) && $at)
                                            {{ $at->format('M d, Y') }}.
                                        @endif
                                        {{ $text }}
                                    </small>

                                    @if(($schedule[$n] ?? '') !== '')
                                        <small class="when">
                                            Schedule: {{ $schedule[$n] }}
                                        </small>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        @endisset

    </section>

    <a class="back-link" href="{{ route('enrollment.create') }}">
        ← Return to Enrollment Form
    </a>

</main>

<footer class="footer">
    © {{ date('Y') }} Ateneo de Zamboanga University · Senior High School
</footer>

</body>
</html>
