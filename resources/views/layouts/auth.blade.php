<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Login') — LP3I Evaluasi Kinerja Dosen</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-lp3i.png') }}">
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('lp3i_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'dark');
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <style>
        :root {
            --accent: #0284C7;
            --accent-glow: rgba(2, 132, 199, 0.4);
            --bg-base: #070D18;
            --card-glass: rgba(13, 22, 41, 0.72);
            --card-border: rgba(56, 189, 248, 0.22);
            --ink: #F1F5F9;
            --muted: #94A3B8;
            --input-bg: rgba(11, 18, 33, 0.65);
            --input-border: rgba(56, 189, 248, 0.2);
            --input-focus: rgba(56, 189, 248, 0.5);
            --btn-grad: linear-gradient(90deg, #0284C7 0%, #2563EB 60%, #1D4ED8 100%);
            --banner-grad: linear-gradient(155deg, #03346E 0%, #02244C 45%, #00152E 100%);
            --banner-card-bg: rgba(255, 255, 255, 0.95);
            --dropdown-bg: #0E1729;
            --building-opacity: 0.22;
            --building-filter: contrast(1.15) brightness(1.1);
            --building-blend: normal;
        }

        [data-theme="light"] {
            --accent: #0284C7;
            --accent-glow: rgba(2, 132, 199, 0.25);
            --bg-base: #EDF5FD;
            --card-glass: rgba(255, 255, 255, 0.85);
            --card-border: rgba(255, 255, 255, 0.9);
            --ink: #0F172A;
            --muted: #64748B;
            --input-bg: rgba(255, 255, 255, 0.85);
            --input-border: #CBD5E1;
            --input-focus: #0284C7;
            --btn-grad: linear-gradient(90deg, #0284C7 0%, #2563EB 60%, #1D4ED8 100%);
            --banner-grad: linear-gradient(155deg, #0284C7 0%, #0369A1 45%, #0F2744 100%);
            --banner-card-bg: #FFFFFF;
            --dropdown-bg: #FFFFFF;
            --building-opacity: 0.18;
            --building-filter: contrast(1.2) brightness(0.95);
            --building-blend: multiply;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-base);
            color: var(--ink);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
            position: relative;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* AMBIENT CELESTIAL GLOWS & SPHERES */
        .bg-glow-container {
            position: fixed;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
            z-index: 0;
        }

        .ambient-orb-1 {
            position: absolute;
            width: 520px;
            height: 520px;
            top: -140px;
            left: -140px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(14, 116, 214, 0.35) 0%, rgba(3, 40, 84, 0.15) 50%, transparent 75%);
            filter: blur(55px);
        }

        .ambient-orb-2 {
            position: absolute;
            width: 580px;
            height: 580px;
            bottom: -180px;
            right: -150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.3) 0%, rgba(30, 58, 138, 0.12) 55%, transparent 80%);
            filter: blur(65px);
        }

        .ambient-orb-3 {
            position: absolute;
            width: 140px;
            height: 140px;
            top: 25%;
            left: 6%;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, rgba(56, 189, 248, 0.4) 0%, rgba(2, 132, 199, 0.15) 60%, transparent 100%);
            border: 1px solid rgba(56, 189, 248, 0.25);
            box-shadow: 0 0 35px rgba(2, 132, 199, 0.25);
        }

        .ambient-orb-4 {
            position: absolute;
            width: 48px;
            height: 48px;
            top: 60%;
            left: 9%;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(56, 189, 248, 0.55) 0%, rgba(3, 105, 161, 0.2) 70%, transparent 100%);
            border: 1px solid rgba(56, 189, 248, 0.3);
            box-shadow: 0 0 20px rgba(56, 189, 248, 0.35);
        }

        .ambient-orb-5 {
            position: absolute;
            width: 30px;
            height: 30px;
            bottom: 35%;
            right: 6%;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(56, 189, 248, 0.6) 0%, rgba(2, 132, 199, 0.2) 70%, transparent 100%);
            border: 1px solid rgba(56, 189, 248, 0.35);
            box-shadow: 0 0 16px rgba(56, 189, 248, 0.4);
        }

        /* LIGHT MODE AMBIENT REFINEMENT */
        [data-theme="light"] .ambient-orb-1 {
            background: radial-gradient(circle, rgba(56, 189, 248, 0.35) 0%, rgba(186, 230, 253, 0.2) 50%, transparent 75%);
            filter: blur(60px);
        }
        [data-theme="light"] .ambient-orb-2 {
            background: radial-gradient(circle, rgba(147, 197, 253, 0.45) 0%, rgba(219, 234, 254, 0.2) 55%, transparent 80%);
            filter: blur(70px);
        }
        [data-theme="light"] .ambient-orb-3 {
            background: radial-gradient(circle at 35% 35%, rgba(255, 255, 255, 0.9) 0%, rgba(186, 230, 253, 0.4) 60%, rgba(255, 255, 255, 0) 100%);
            border: 1.5px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 30px rgba(2, 132, 199, 0.15), inset 0 2px 4px rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
        }
        [data-theme="light"] .ambient-orb-4 {
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.95) 0%, rgba(186, 230, 253, 0.5) 70%, transparent 100%);
            border: 1px solid rgba(255, 255, 255, 0.85);
            box-shadow: 0 6px 18px rgba(2, 132, 199, 0.15);
        }
        [data-theme="light"] .ambient-orb-5 {
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.95) 0%, rgba(56, 189, 248, 0.3) 70%, transparent 100%);
            border: 1px solid rgba(255, 255, 255, 0.85);
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15);
        }

        /* FLOATING THEME TOGGLE (Sun/Moon Orb) */
        .auth-theme-toggle {
            position: fixed;
            top: 24px;
            right: 24px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 1.5px solid var(--card-border);
            background: radial-gradient(circle at 35% 35%, #F59E0B 0%, #D97706 70%, #92400E 100%);
            box-shadow: 0 0 22px rgba(245, 158, 11, 0.5), 0 4px 14px rgba(0,0,0,0.35);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            z-index: 100;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        [data-theme="light"] .auth-theme-toggle {
            background: radial-gradient(circle at 35% 35%, #0284C7 0%, #0369A1 70%, #0F2744 100%);
            box-shadow: 0 0 20px rgba(2, 132, 199, 0.4), 0 4px 14px rgba(15, 39, 68, 0.25);
            border-color: rgba(255, 255, 255, 0.9);
        }

        .auth-theme-toggle:hover {
            transform: scale(1.12) rotate(15deg);
        }

        [data-theme="dark"] .theme-icon-light { display: inline-flex; }
        [data-theme="dark"] .theme-icon-dark { display: none; }
        [data-theme="light"] .theme-icon-light { display: none; }
        [data-theme="light"] .theme-icon-dark { display: inline-flex; color: #FFFFFF; font-size: 21px; }

        /* MAIN LOGIN CONTAINER */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 960px;
            margin: auto;
        }

        .login-card {
            background: var(--card-glass);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border-radius: 24px;
            border: 1.5px solid var(--card-border);
            box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.65), 
                        0 0 40px rgba(2, 132, 199, 0.18),
                        inset 0 1px 1px rgba(255, 255, 255, 0.15);
            overflow: hidden;
            display: flex;
            min-height: 540px;
            position: relative;
            transition: all 0.3s ease;
        }

        [data-theme="light"] .login-card {
            box-shadow: 0 30px 70px -12px rgba(2, 132, 199, 0.2), 
                        0 0 0 1px rgba(186, 230, 253, 0.6),
                        inset 0 2px 2px rgba(255, 255, 255, 1);
        }

        /* LEFT BANNER */
        .login-banner {
            width: 48%;
            background: var(--banner-grad);
            color: #ffffff;
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            border-right: 1px solid rgba(56, 189, 248, 0.15);
        }

        .banner-body {
            margin: auto 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .banner-footer {
            margin-top: auto;
            width: 100%;
        }

        [data-theme="light"] .login-banner {
            border-right: 1px solid rgba(255, 255, 255, 0.25);
        }

        .login-banner::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.32) 0%, transparent 70%);
            pointer-events: none;
        }

        .login-banner::after {
            content: '';
            position: absolute;
            bottom: -60px;
            left: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.3) 0%, transparent 70%);
            filter: blur(25px);
            pointer-events: none;
        }

        .banner-brand-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .banner-brand-text {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .banner-logo-box {
            background: #FFFFFF;
            border-radius: 18px;
            width: 72px;
            height: 72px;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35), 0 0 20px rgba(56, 189, 248, 0.3);
            margin: 0 auto 16px auto;
            position: relative;
            z-index: 2;
        }

        .banner-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .banner-brand-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.6px;
            color: #FFFFFF;
            margin-bottom: 2px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .banner-brand-subtitle {
            font-size: 12.5px;
            font-weight: 500;
            color: #BAE6FD;
            letter-spacing: 0.3px;
            margin-bottom: 22px;
        }

        .banner-tag-academic {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(2, 132, 199, 0.28);
            border: 1px solid rgba(186, 230, 253, 0.4);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            color: #E0F2FE;
            backdrop-filter: blur(8px);
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .banner-main-title {
            font-size: 25px;
            font-weight: 800;
            line-height: 1.3;
            color: #FFFFFF;
            margin-bottom: 12px;
            letter-spacing: -0.3px;
        }

        .banner-description {
            font-size: 12.5px;
            color: rgba(240, 249, 255, 0.9);
            line-height: 1.6;
            margin-bottom: 24px;
            max-width: 320px;
        }

        .banner-badge-secure {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(2, 132, 199, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(186, 230, 253, 0.35);
            padding: 8px 18px;
            border-radius: 12px;
            font-size: 12px;
            color: #F0F9FF;
            font-weight: 600;
        }

        .banner-divider {
            width: 36px;
            height: 2px;
            background: rgba(255, 255, 255, 0.28);
            margin: 20px auto 8px auto;
            border-radius: 2px;
        }

        .banner-copyright {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.65);
            letter-spacing: 0.2px;
        }

        /* RIGHT FORM AREA */
        .login-form-area {
            width: 52%;
            padding: 44px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: rgba(10, 18, 36, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* LP3I BUILDING WATERMARK (LEFT-ALIGNED UPPER ARCHITECTURE) */
        .login-form-area::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("{{ asset('images/gedung-lp3i-right.png') }}");
            background-position: left 0 top 0;
            background-size: cover;
            background-repeat: no-repeat;
            opacity: var(--building-opacity);
            filter: var(--building-filter);
            mix-blend-mode: var(--building-blend);
            pointer-events: none;
            z-index: 0;
            transition: opacity 0.3s ease;
        }

        [data-theme="light"] .login-form-area {
            background: rgba(255, 255, 255, 0.75);
        }

        .form-content-inner {
            position: relative;
            z-index: 2;
        }

        .form-header {
            margin-bottom: 24px;
        }

        .form-header h2 {
            font-size: 24px;
            font-weight: 800;
            color: var(--ink);
            letter-spacing: -0.4px;
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--ink);
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            color: var(--muted);
            font-size: 16px;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }

        .form-control {
            width: 100%;
            height: 48px;
            padding: 10px 40px 10px 44px;
            font-size: 13.5px;
            border: 1.5px solid var(--input-border);
            border-radius: 12px;
            outline: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: inherit;
            background: var(--input-bg);
            color: var(--ink);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        }

        .form-control:focus {
            background: var(--input-bg);
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.25), 0 4px 14px rgba(2, 132, 199, 0.15);
        }

        [data-theme="light"] .form-control {
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04), inset 0 1px 2px rgba(255, 255, 255, 0.8);
        }

        [data-theme="light"] .form-control:focus {
            box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.15), 0 4px 12px rgba(2, 132, 199, 0.1);
        }

        .form-control::placeholder {
            color: var(--muted);
            font-size: 13px;
            opacity: 0.8;
        }

        .btn-submit {
            width: 100%;
            height: 48px;
            background: var(--btn-grad);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.45);
        }

        .btn-submit:hover {
            background: linear-gradient(90deg, #0369A1 0%, #1D4ED8 60%, #1E40AF 100%);
            box-shadow: 0 8px 26px rgba(2, 132, 199, 0.6);
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* AUTO-DISMISS ALERT WITH SMOOTH FADE & PROGRESS */
        .alert {
            position: relative;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            overflow: hidden;
            transition: opacity 0.5s ease, transform 0.5s ease, max-height 0.5s ease, margin-bottom 0.5s ease, padding 0.5s ease;
            max-height: 120px;
            z-index: 10;
        }

        .alert.fade-out {
            opacity: 0;
            transform: translateY(-8px);
            max-height: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            border-width: 0;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.18);
            color: #F87171;
            border: 1px solid rgba(239, 68, 68, 0.35);
        }

        [data-theme="light"] .alert-error {
            background: #FEE2E2;
            color: #DC2626;
            border: 1px solid #FCA5A5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.18);
            color: #34D399;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        [data-theme="light"] .alert-success {
            background: #DCFCE7;
            color: #16A34A;
            border: 1px solid #86EFAC;
        }

        .alert-close {
            background: none;
            border: none;
            color: inherit;
            opacity: 0.6;
            cursor: pointer;
            font-size: 14px;
            padding: 2px 6px;
            border-radius: 4px;
            line-height: 1;
            transition: opacity 0.15s;
        }

        .alert-close:hover {
            opacity: 1;
        }

        /* Responsive Breakpoints */
        @media (max-width: 820px) {
            body {
                padding: 16px 12px;
                align-items: center;
                padding-top: 16px;
            }

            .ambient-orb-3,
            .ambient-orb-4,
            .ambient-orb-5 {
                display: none;
            }

            .login-container {
                max-width: 420px;
            }

            .login-card {
                flex-direction: column;
                max-width: 100%;
                border-radius: 18px;
                min-height: auto;
                width: 100%;
                box-shadow: 0 15px 40px -8px rgba(0, 0, 0, 0.5), 
                            0 0 25px rgba(2, 132, 199, 0.12),
                            inset 0 1px 1px rgba(255, 255, 255, 0.1);
            }

            .login-banner {
                width: 100%;
                padding: 16px 20px;
                border-radius: 18px 18px 0 0;
                border-right: none;
                border-bottom: 1px solid rgba(56, 189, 248, 0.15);
                justify-content: center;
                align-items: center;
                min-height: auto;
            }

            [data-theme="light"] .login-banner {
                border-bottom: 1px solid rgba(255, 255, 255, 0.25);
            }

            .banner-body {
                margin: 0;
            }

            .banner-brand-wrapper {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 14px !important;
                text-align: left !important;
            }

            .banner-logo-box {
                width: 44px !important;
                height: 44px !important;
                border-radius: 12px !important;
                padding: 5px !important;
                margin: 0 !important;
                flex-shrink: 0 !important;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25), 0 0 12px rgba(56, 189, 248, 0.2) !important;
            }

            .banner-brand-text {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                text-align: left !important;
            }

            .banner-brand-title {
                font-size: 16px !important;
                font-weight: 800 !important;
                margin-bottom: 0 !important;
                letter-spacing: 0.4px !important;
                line-height: 1.2 !important;
            }

            /* Hide verbose & decorative text on mobile so the form fits on a single phone screen without scrolling */
            .banner-brand-subtitle,
            .banner-tag-academic,
            .banner-main-title,
            .banner-description,
            .banner-badge-secure,
            .banner-footer {
                display: none !important;
            }

            .login-form-area {
                width: 100%;
                padding: 20px 20px 24px 20px;
            }

            .auth-theme-toggle {
                top: 14px;
                right: 14px;
                width: 38px;
                height: 38px;
                font-size: 16px;
                box-shadow: 0 0 14px rgba(245, 158, 11, 0.4), 0 2px 8px rgba(0,0,0,0.25);
            }

            [data-theme="light"] .auth-theme-toggle {
                box-shadow: 0 0 14px rgba(2, 132, 199, 0.35), 0 2px 8px rgba(15, 39, 68, 0.2);
            }

            .form-header {
                margin-bottom: 14px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .form-label {
                font-size: 11.5px;
                margin-bottom: 4px;
            }

            .form-control {
                height: 44px;
                font-size: 13px;
                padding: 8px 12px 8px 38px;
                border-radius: 10px;
            }

            .input-icon {
                font-size: 14px;
                left: 12px;
            }

            .form-actions-row {
                margin-bottom: 10px;
                margin-top: 2px;
            }

            .remember-me {
                font-size: 12px;
            }

            .btn-submit {
                height: 44px;
                padding: 10px 18px;
                font-size: 13.5px;
                border-radius: 10px;
                margin-top: 6px;
            }

            .alert {
                font-size: 12px;
                padding: 10px 12px;
                margin-bottom: 12px;
            }
        }

        /* Extra small screens (< 380px) */
        @media (max-width: 380px) {
            body {
                padding: 8px 6px;
                padding-top: 12px;
            }

            .login-card {
                border-radius: 14px;
            }

            .login-banner {
                padding: 12px 16px;
                border-radius: 14px 14px 0 0;
            }

            .banner-logo-box {
                width: 36px;
                height: 36px;
                border-radius: 8px;
            }

            .banner-brand-title {
                font-size: 14px;
            }

            .login-form-area {
                padding: 16px 14px 20px 14px;
            }

            .form-header h2 {
                font-size: 16px;
            }

            .form-header p {
                font-size: 11px;
            }

            .auth-theme-toggle {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .form-control {
                height: 42px;
                font-size: 12.5px;
            }

            .btn-submit {
                height: 42px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <!-- BACKGROUND AMBIENT ORBS -->
    <div class="bg-glow-container">
        <div class="ambient-orb-1"></div>
        <div class="ambient-orb-2"></div>
        <div class="ambient-orb-3"></div>
        <div class="ambient-orb-4"></div>
        <div class="ambient-orb-5"></div>
    </div>

    <!-- FLOATING THEME TOGGLE (SUN/MOON ORB) -->
    <button type="button" class="auth-theme-toggle" onclick="toggleTheme()" title="Ganti Tema Gelap / Terang">
        <span class="theme-icon-light">☀️</span>
        <span class="theme-icon-dark">🌙</span>
    </button>

    <div class="login-container">
        @yield('content')
    </div>

    <script>
        // Auto dismiss alert popup after 5 seconds
        document.addEventListener('DOMContentLoaded', () => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    dismissAlert(alert);
                }, 5000);
            });
        });

        function dismissAlert(alertElement) {
            if (!alertElement) return;
            alertElement.classList.add('fade-out');
            setTimeout(() => {
                if (alertElement.parentNode) {
                    alertElement.parentNode.removeChild(alertElement);
                }
            }, 550);
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('lp3i_theme', newTheme);
        }
    </script>
</body>
</html>
