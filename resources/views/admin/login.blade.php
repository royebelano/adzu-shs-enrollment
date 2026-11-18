
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Staff Login | AdZU Senior High</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --navy: #071d3a;
            --navy-light: #102f56;
            --blue: #245b91;
            --gold: #e6b85c;
            --text: #172b45;
            --muted: #718096;
            --border: #e3eaf2;
        }

        body {
            min-height: 100vh;
            min-height: 100dvh;
            font-family: 'DM Sans', sans-serif;
            background: #f3f6fa;
            color: var(--text);
        }

        /* MAIN LAYOUT */

        .page-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            min-height: 100dvh;
        }

        /* LEFT PANEL */

        .welcome-panel {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            padding: 55px 8%;
            color: white;

            background:
                radial-gradient(
                    circle at 85% 20%,
                    rgba(51, 111, 166, .35),
                    transparent 35%
                ),
                linear-gradient(145deg, #06172e, #0b2c50 65%, #123b64);
        }

        .welcome-panel::before {
            content: "";
            position: absolute;
            width: 520px;
            height: 520px;
            right: -220px;
            bottom: -230px;
            border: 1px solid rgba(230, 184, 92, .20);
            border-radius: 50%;
            box-shadow:
                0 0 0 45px rgba(230, 184, 92, .035),
                0 0 0 90px rgba(230, 184, 92, .025);
        }

        .welcome-panel::after {
            content: "";
            position: absolute;
            width: 250px;
            height: 250px;
            top: 35%;
            left: -160px;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 50%;
        }

        .brand {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand img {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }

        .brand-text strong {
            display: block;
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            color: white;
        }

        .brand-text span {
            display: block;
            margin-top: 3px;
            color: #c2d2e5;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            max-width: 540px;
            margin: 70px 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 25px;
            color: var(--gold);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2.2px;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 25px;
            height: 2px;
            background: var(--gold);
        }

        .welcome-content h1 {
            margin-bottom: 22px;
            font-family: 'Playfair Display', serif;
            font-size: clamp(34px, 4vw, 55px);
            line-height: 1.17;
            font-weight: 700;
            color: #fff;
        }

        .welcome-content h1 span {
            color: var(--gold);
        }

        .welcome-content p {
            max-width: 420px;
            color: #c2d2e5;
            font-size: 14px;
            line-height: 1.9;
        }

        .welcome-divider {
            width: 65px;
            height: 3px;
            margin-top: 32px;
            background: var(--gold);
            border-radius: 10px;
        }

        .welcome-footer {
            position: relative;
            z-index: 1;
            color: #9eb3cb;
            font-size: 11px;
            line-height: 1.7;
        }

        /* RIGHT PANEL */

        .login-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 45px 30px;
            background: #f5f7fb;
        }

        .login-container {
            width: 100%;
            max-width: 430px;
        }

        .mobile-brand {
            display: none;
        }

        .login-heading {
            margin-bottom: 30px;
        }

        .login-heading .small-label {
            margin-bottom: 10px;
            color: #b18437;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .login-heading h2 {
            margin-bottom: 9px;
            color: var(--navy);
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            font-weight: 700;
        }

        .login-heading p {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        /* FORM CARD */

        .login-card {
            padding: 30px;
            border: 1px solid #e8edf3;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 15px 45px rgba(20, 45, 75, .07);
        }

        .field-group {
            margin-bottom: 21px;
        }

        .field-group label {
            display: block;
            margin-bottom: 9px;
            color: #34465d;
            font-size: 11px;
            font-weight: 700;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            width: 18px;
            height: 18px;
            color: #8b9bb0;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .input-wrapper input {
            width: 100%;
            height: 49px;
            padding: 0 44px;
            border: 1px solid var(--border);
            border-radius: 9px;
            outline: none;
            background: #f9fbfd;
            color: var(--text);
            font-family: inherit;
            font-size: 13px;
            transition: .2s ease;
        }

        .input-wrapper input::placeholder {
            color: #a3afbd;
        }

        .input-wrapper input:focus {
            border-color: #477eae;
            background: white;
            box-shadow: 0 0 0 4px rgba(71, 126, 174, .10);
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 13px;
            display: grid;
            place-items: center;
            width: 25px;
            height: 25px;
            border: 0;
            background: transparent;
            color: #8091a5;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .password-toggle:hover {
            color: var(--blue);
        }

        .password-toggle svg {
            width: 17px;
            height: 17px;
        }

        /* REMEMBER */

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 3px 0 23px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #65758a;
            font-size: 12px;
            cursor: pointer;
        }

        .remember input {
            width: 15px;
            height: 15px;
            accent-color: var(--blue);
            cursor: pointer;
        }

        /* BUTTON */

        .login-submit {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            height: 49px;
            border: 0;
            border-radius: 9px;
            background: linear-gradient(110deg, #102b4e, #1c4774);
            color: #fff;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 7px 17px rgba(16, 43, 78, .17);
            transition: .2s ease;
        }

        .login-submit:hover {
            background: linear-gradient(110deg, #173b65, #285e91);
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(16, 43, 78, .22);
        }

        .login-submit svg {
            width: 17px;
            height: 17px;
        }

        .login-submit:focus-visible {
            outline: 3px solid var(--gold);
            outline-offset: 3px;
        }

        /* ERRORS */

        .error {
            margin-top: 7px;
            color: #c03939;
            font-size: 11px;
        }

        .login-alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border: 1px solid #f0c8c8;
            border-radius: 8px;
            background: #fff2f2;
            color: #a52b2b;
            font-size: 12px;
        }

        /* BOTTOM NOTE */

        .security-note {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            color: #8a98a9;
            font-size: 11px;
            text-align: center;
        }

        .security-note svg {
            width: 15px;
            height: 15px;
            color: #4b9875;
        }

        .copyright {
            margin-top: 35px;
            color: #9aa6b5;
            font-size: 10px;
            text-align: center;
        }

        /* RESPONSIVE */

        @media (max-width: 850px) {
            .page-wrapper {
                grid-template-columns: 42% 58%;
            }

            .welcome-panel {
                padding: 40px 7%;
            }

            .welcome-content h1 {
                font-size: 37px;
            }

            .login-panel {
                padding: 35px 25px;
            }
        }

        @media (max-width: 650px) {
            .page-wrapper {
                display: block;
            }

            .welcome-panel {
                display: none;
            }

            .login-panel {
                min-height: 100vh;
                min-height: 100dvh;
                padding: 35px 20px;
            }

            .login-container {
                max-width: 420px;
            }

            .mobile-brand {
                display: block;
                margin-bottom: 35px;
                text-align: center;
            }

            .mobile-brand img {
                width: 65px;
                height: 65px;
                margin-bottom: 10px;
                object-fit: contain;
            }

            .mobile-brand strong {
                display: block;
                color: var(--navy);
                font-family: 'Playfair Display', serif;
                font-size: 18px;
            }

            .mobile-brand span {
                display: block;
                margin-top: 5px;
                color: #a17a36;
                font-size: 9px;
                font-weight: 800;
                letter-spacing: 1.5px;
                text-transform: uppercase;
            }

            .login-heading h2 {
                font-size: 27px;
            }

            .login-card {
                padding: 24px 20px;
            }

            .copyright {
                margin-top: 25px;
            }
        }

        @media (max-width: 360px) {
            .login-panel {
                padding: 25px 14px;
            }

            .login-card {
                padding: 20px 16px;
            }

            .form-options {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<div class="page-wrapper">

    <!-- LEFT WELCOME PANEL -->

    <section class="welcome-panel">

        <div class="brand">
            <img
                src="{{ asset('logo_adzu.jpg') }}"
                alt="AdZU Logo"
            >

            <div class="brand-text">
                <strong>Ateneo de Zamboanga</strong>
                <span>University</span>
            </div>
        </div>

        <div class="welcome-content">

            <div class="eyebrow">
                Senior High School
            </div>

            <h1>
                Welcome to the
                <span>e-Admissions</span>
                Portal.
            </h1>

            <p>
                An official admissions management portal designed
                to support the enrollment process of Ateneo de
                Zamboanga University Senior High School.
            </p>

            <div class="welcome-divider"></div>

        </div>

        <div class="welcome-footer">
            Ateneo de Zamboanga University<br>
            Senior High School Admissions Office
        </div>

    </section>


    <!-- RIGHT LOGIN PANEL -->

    <main class="login-panel">

        <div class="login-container">

            <!-- MOBILE BRANDING -->

            <div class="mobile-brand">
                <img
                    src="{{ asset('logo_adzu.jpg') }}"
                    alt="AdZU Logo"
                >

                <strong>Senior High School</strong>

                <span>Ateneo de Zamboanga University</span>
            </div>


            <!-- LOGIN HEADING -->

            <div class="login-heading">

                <div class="small-label">
                    Admissions Administration
                </div>

                <h2>Staff Login</h2>

                <p>
                    Sign in using your institutional credentials
                    to access the admissions management system.
                </p>

            </div>


            <!-- LOGIN FORM -->

            <form
                class="login-card"
                method="POST"
                action="{{ route('admin.login.submit') }}"
            >

                @csrf

                @if(session('error'))
                    <div class="login-alert">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- USERNAME -->

                <div class="field-group">

                    <label for="email">
                        Institutional Email
                    </label>

                    <div class="input-wrapper">

                        <svg class="input-icon" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                            <path d="m3 7 9 6 9-6"/>
                        </svg>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="name@adzu.edu.ph"
                            required
                            autofocus
                            autocomplete="username"
                        >

                    </div>

                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror

                </div>


                <!-- PASSWORD -->

                <div class="field-group">

                    <label for="password">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <svg class="input-icon" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="1.8" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="3" y="10" width="18" height="11" rx="2"/>
                            <path d="M7 10V7a5 5 0 0 1 10 0v3"/>
                        </svg>

                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                            aria-label="Show password"
                        >
                            <svg id="eyeIcon" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>

                    </div>

                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror

                </div>


                <!-- REMEMBER ME -->

                <div class="form-options">

                    <label class="remember">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                        >
                        Remember me
                    </label>

                </div>


                <!-- SUBMIT BUTTON -->

                <button type="submit" class="login-submit">

                    <span>Sign In to Your Account</span>

                    <svg viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>
                    </svg>

                </button>

            </form>


            <!-- SECURITY NOTE -->

            <div class="security-note">

                <svg viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>

                Authorized personnel only. Secure access protected.

            </div>

            <div class="copyright">
                &copy; {{ date('Y') }} Ateneo de Zamboanga University.
                All rights reserved.
            </div>

        </div>

    </main>

</div>


<!-- SHOW / HIDE PASSWORD -->

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const password = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        toggle.addEventListener('click', function () {

            const isHidden = password.type === 'password';

            password.type = isHidden ? 'text' : 'password';

            toggle.setAttribute(
                'aria-label',
                isHidden ? 'Hide password' : 'Show password'
            );

            eyeIcon.innerHTML = isHidden
                ? '<path d="M3 3l18 18"/><path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.4 0 10 7 10 7a16 16 0 0 1-3 3.7"/><path d="M6.6 6.6C3.6 8.5 2 12 2 12s3.6 7 10 7c1.2 0 2.3-.3 3.3-.7"/>'
                : '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>';
        });

    });
</script>

</body>
</html>
