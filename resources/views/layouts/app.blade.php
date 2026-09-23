<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — LP3I Evaluasi Kinerja Dosen</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-lp3i.png') }}">
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('lp3i_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <style>
        :root {
            --navy: #002B49;
            --navy-dark: #001B30;
            --accent: #0284C7;
            --accent-hover: #0369A1;
            --bg: #F8FAFC;
            --card-bg: #FFFFFF;
            --border: #E2E8F0;
            --ink: #0F172A;
            --muted: #64748B;
            --green: #10B981;
            --green-bg: #ECFDF5;
            --red: #EF4444;
            --red-bg: #FEF2F2;
            --blue-bg: #F0F9FF;
            --purple: #8B5CF6;
            --purple-bg: #F5F3FF;
            --amber: #F59E0B;
            --amber-bg: #FFFBEB;
            --th-bg: #F8FAFC;
            --tr-hover: #F1F5F9;
            --input-bg: #FFFFFF;
            --sidebar-bg: #002B49;
            --sidebar-border: rgba(255, 255, 255, 0.08);
            --sidebar-link: #CBD5E1;
            --sidebar-hover: rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] {
            --navy: #38BDF8;
            --navy-dark: #0284C7;
            --accent: #38BDF8;
            --accent-hover: #7DD3FC;
            --bg: #0B0F19;
            --card-bg: #131B2E;
            --border: #232F48;
            --ink: #F1F5F9;
            --muted: #94A3B8;
            --green: #34D399;
            --green-bg: rgba(16, 185, 129, 0.16);
            --red: #F87171;
            --red-bg: rgba(239, 68, 68, 0.16);
            --blue-bg: rgba(56, 189, 248, 0.14);
            --purple: #C084FC;
            --purple-bg: rgba(192, 132, 252, 0.15);
            --amber: #FBBF24;
            --amber-bg: rgba(251, 191, 36, 0.15);
            --th-bg: #0E1524;
            --tr-hover: rgba(56, 189, 248, 0.06);
            --input-bg: #0B0F19;
            --sidebar-bg: #0A1124;
            --sidebar-border: rgba(255, 255, 255, 0.06);
            --sidebar-link: #94A3B8;
            --sidebar-hover: rgba(255, 255, 255, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
            display: flex;
            min-height: 100vh;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: var(--sidebar-bg);
            color: var(--sidebar-link);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: all 0.25s ease;
            z-index: 50;
            border-right: 1px solid var(--sidebar-border);
        }
        .sidebar-brand {
            padding: 20px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: 800;
            border-bottom: 1px solid var(--sidebar-border);
            letter-spacing: 0.5px;
        }
        .sidebar-nav {
            padding: 18px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .nav-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--muted);
            padding: 10px 8px 4px;
            font-weight: 700;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 7px;
            color: var(--sidebar-link);
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 500;
            transition: all 0.15s ease;
        }
        .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .nav-link.active {
            background: var(--accent);
            color: #fff;
            font-weight: 600;
        }
        .nav-icon {
            font-size: 15px;
            width: 18px;
            display: inline-flex;
            justify-content: center;
        }
        .sidebar-footer {
            padding: 14px;
            border-top: 1px solid var(--sidebar-border);
            font-size: 11px;
            color: var(--muted);
            text-align: center;
        }

        /* MAIN WRAPPER */
        .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            overflow-x: hidden;
        }

        /* TOPBAR */
        .topbar {
            height: 60px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 40;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .toggle-sidebar {
            background: none;
            border: none;
            font-size: 18px;
            color: var(--muted);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 5px;
        }
        .toggle-sidebar:hover {
            background: var(--bg);
            color: var(--ink);
        }
        .topbar-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--ink);
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-theme-toggle {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--ink);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.2s ease;
        }
        .btn-theme-toggle:hover {
            border-color: var(--accent);
            transform: scale(1.05);
        }
        [data-theme="dark"] .theme-icon-light { display: inline-flex; }
        [data-theme="dark"] .theme-icon-dark { display: none; }
        [data-theme="light"] .theme-icon-light { display: none; }
        [data-theme="light"] .theme-icon-dark { display: inline-flex; }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            cursor: pointer;
        }
        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--blue-bg);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 12px;
            border: 2px solid var(--border);
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        .user-info {
            line-height: 1.2;
        }
        .user-name {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--ink);
        }
        .user-role {
            font-size: 10px;
            color: var(--muted);
            text-transform: capitalize;
        }

        /* MAIN CONTENT AREA */
        .content {
            flex: 1;
            padding: 24px;
            background: var(--bg);
            transition: background-color 0.2s ease;
        }
        .page-header {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 3px;
        }
        .page-subtitle {
            font-size: 12px;
            color: var(--muted);
        }

        /* STAT CARDS */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 22px;
        }
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            transition: transform 0.15s, box-shadow 0.15s, background-color 0.2s, border-color 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.05);
        }
        .stat-ico {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .ico-blue { background: var(--blue-bg); color: var(--accent); }
        .ico-green { background: var(--green-bg); color: var(--green); }
        .ico-red { background: var(--red-bg); color: var(--red); }
        .ico-purple { background: var(--purple-bg); color: var(--purple); }
        .ico-amber { background: var(--amber-bg); color: var(--amber); }
        .stat-num {
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.1;
        }
        .stat-lbl {
            font-size: 11.5px;
            color: var(--muted);
            margin-top: 3px;
        }

        /* PANELS & CARDS */
        .panel {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
            margin-bottom: 22px;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .panel-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--ink);
        }

        /* TABLES & HORIZONTAL SCROLL */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        .table-responsive::-webkit-scrollbar-track {
            background: var(--bg);
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 10px;
        }
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: var(--muted);
        }
        table {
            width: 100%;
            min-width: 680px;
            border-collapse: collapse;
            font-size: 12.5px;
        }
        th {
            text-align: left;
            color: var(--muted);
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 11px 14px;
            border-bottom: 1.5px solid var(--border);
            background: var(--th-bg);
            white-space: nowrap;
        }
        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: var(--ink);
        }
        tr:hover td {
            background: var(--tr-hover);
        }

        /* BADGES */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 10.5px;
            font-weight: 600;
        }
        .badge-green { background: var(--green-bg); color: var(--green); }
        .badge-red { background: var(--red-bg); color: var(--red); }
        .badge-blue { background: var(--blue-bg); color: var(--accent); }
        .badge-purple { background: var(--purple-bg); color: var(--purple); }
        .badge-gray { background: var(--border); color: var(--muted); }
        .badge-amber { background: var(--amber-bg); color: var(--amber); }

        /* BUTTONS */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s;
            font-family: inherit;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-navy { background: #002B49; color: #fff; }
        .btn-navy:hover { background: #001B30; }
        .btn-secondary { background: var(--card-bg); border-color: var(--border); color: var(--ink); }
        .btn-secondary:hover { background: var(--bg); border-color: var(--accent); color: var(--accent); }
        .btn-ghost { background: var(--card-bg); border-color: var(--border); color: var(--ink); }
        .btn-ghost:hover { background: var(--bg); }
        .btn-danger { background: var(--red); color: #fff; }
        .btn-danger:hover { opacity: 0.9; }
        .btn-success { background: var(--green); color: #fff; }
        .btn-success:hover { opacity: 0.9; }
        .btn-sm { padding: 4px 8px; font-size: 11px; }

        .btn-icon {
            width: 28px;
            height: 28px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            border: 1px solid var(--border);
            background: var(--card-bg);
            color: var(--ink);
            cursor: pointer;
            text-decoration: none;
        }
        .btn-icon:hover { background: var(--bg); }
        .btn-icon.text-danger:hover { background: var(--red-bg); color: var(--red); border-color: var(--red); }
        .btn-icon.text-primary:hover { background: var(--blue-bg); color: var(--accent); border-color: var(--accent); }

        /* FORMS */
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 11.5px; font-weight: 600; margin-bottom: 5px; color: var(--ink); }
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid var(--border);
            font-size: 12.5px;
            outline: none;
            font-family: inherit;
            background: var(--input-bg);
            color: var(--ink);
            transition: border-color 0.15s, background-color 0.15s;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
        .form-control:disabled,
        .form-control[disabled],
        .form-control[readonly] {
            background: var(--bg) !important;
            color: var(--muted) !important;
            border-color: var(--border) !important;
            cursor: not-allowed;
            opacity: 0.85;
        }
        textarea.form-control { min-height: 80px; resize: vertical; }

        /* STEPPER & INFO CARDS */
        .stepper-container {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 18px 24px;
            margin-bottom: 22px;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        .info-card-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 18px 24px;
            margin-bottom: 22px;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        /* TABS */
        .tabs {
            display: flex;
            gap: 6px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 18px;
        }
        .tab-item {
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            color: var(--muted);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -1px;
            transition: all 0.15s;
        }
        .tab-item:hover { color: var(--ink); }
        .tab-item.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        /* ALERTS */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 12px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .alert-success { background: var(--green-bg); color: var(--green); border: 1px solid rgba(16, 185, 129, 0.3); }
        .alert-danger { background: var(--red-bg); color: var(--red); border: 1px solid rgba(239, 68, 68, 0.3); }
        .alert-warning { background: var(--amber-bg); color: var(--amber); border: 1px solid rgba(245, 158, 11, 0.3); }

        /* MODAL */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.65);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
            backdrop-filter: blur(2px);
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: modalFadeIn 0.2s ease;
        }
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-title { font-size: 14px; font-weight: 700; color: var(--ink); }
        .modal-close { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--muted); }
        .modal-close:hover { color: var(--ink); }
        .modal-body { padding: 20px; }
        .modal-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            background: var(--th-bg);
        }

        /* SIDEBAR OVERLAY FOR MOBILE */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(3px);
            -webkit-backdrop-filter: blur(3px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
            z-index: 45;
            pointer-events: none;
        }
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        /* PAGINATION */
        .pagination-container {
            margin-top: 14px;
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 6px;
        }

        /* COMPREHENSIVE RESPONSIVE STYLING (Mobile, Tablet, Laptop, PC) */
        @media (min-width: 1440px) {
            .content {
                padding: 32px 36px;
                max-width: 1600px;
                margin: 0 auto;
                width: 100%;
            }
            .stat-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }
        }

        @media (max-width: 1024px) {
            .stat-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 14px;
            }
        }

        @media (max-width: 900px) {
            .sidebar {
                position: fixed;
                top: 0; 
                bottom: 0; 
                left: 0;
                transform: translateX(-100%);
                transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
                z-index: 50;
                box-shadow: 0 0 30px rgba(0, 0, 0, 0.45);
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 16px 14px;
            }
            .topbar {
                padding: 0 14px;
                height: 56px;
            }
            .topbar-title {
                font-size: 13px;
                max-width: 200px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                margin-bottom: 16px;
            }
            .page-header > div:last-child {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            .tabs {
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 2px;
                scrollbar-width: none;
            }
            .tabs::-webkit-scrollbar {
                display: none;
            }
            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .stat-card {
                padding: 12px;
                gap: 10px;
            }
            .stat-ico {
                width: 38px;
                height: 38px;
                font-size: 17px;
            }
            .stat-num {
                font-size: 18px;
            }
            .stat-lbl {
                font-size: 10.5px;
            }
            .panel {
                padding: 16px;
                border-radius: 8px;
            }
            .panel-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .modal-box {
                margin: 10px;
                max-width: 100%;
                border-radius: 10px;
            }
            .modal-header, .modal-body, .modal-footer {
                padding: 14px 16px;
            }
            .filter-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 8px !important;
            }
            .filter-search {
                width: 100% !important;
            }
            .filter-select {
                width: 100% !important;
            }
        }

        @media (max-width: 480px) {
            .topbar-title {
                display: none;
            }
            .user-role {
                display: none;
            }
            .user-name {
                font-size: 11.5px;
                max-width: 90px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .stat-grid {
                grid-template-columns: 1fr;
            }
            .modal-overlay {
                padding: 8px;
            }
        }
    </style>
</head>
<body>

    <!-- SIDEBAR OVERLAY -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo-lp3i.png') }}" alt="LP3I Logo" style="height: 32px; width: auto; object-fit: contain; background: #ffffff; padding: 2px 5px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
            <div style="display: flex; flex-direction: column;">
                <span style="font-size: 13.5px; font-weight: 800; line-height: 1.1; letter-spacing: 0.5px; color: #fff;">LP3I PURWAKARTA</span>
                <span style="font-size: 9.5px; font-weight: 500; color: #93C5FD; letter-spacing: 0.2px;">Sistem Evaluasi Dosen</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            @php $user = auth()->user(); @endphp

            @if($user->isAdmin())
                <div class="nav-label">Menu Utama</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">▦</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.master.index') }}" class="nav-link {{ request()->routeIs('admin.master.*') ? 'active' : '' }}">
                    <span class="nav-icon">🗂</span>
                    <span>Master Data</span>
                </a>
                <a href="{{ route('admin.kuesioner.index') }}" class="nav-link {{ request()->routeIs('admin.kuesioner.*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span>
                    <span>Kelola Kuesioner</span>
                </a>
                <a href="{{ route('admin.laporan.index') }}" class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                    <span class="nav-icon">📈</span>
                    <span>Laporan & Rekap</span>
                </a>
            @elseif($user->isDosen())
                <div class="nav-label">Menu Dosen</div>
                <a href="{{ route('dosen.dashboard') }}" class="nav-link {{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">▦</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('dosen.evaluasi.index') }}" class="nav-link {{ request()->routeIs('dosen.evaluasi.*') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span>
                    <span>Hasil Evaluasi</span>
                </a>
            @elseif($user->isMahasiswa())
                <div class="nav-label">Menu Mahasiswa</div>
                <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">▦</span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('mahasiswa.kuesioner.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.kuesioner.*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span>
                    <span>Isi Kuesioner</span>
                </a>
                <a href="{{ route('mahasiswa.history.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.history.*') ? 'active' : '' }}">
                    <span class="nav-icon">🕘</span>
                    <span>Riwayat Evaluasi</span>
                </a>
            @endif

            <div class="nav-label" style="margin-top: 14px;">Akun</div>
            <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                <span class="nav-icon">👤</span>
                <span>Profil Saya</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            LP3I Purwakarta &bull; v1.0.0
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="wrapper">
        <header class="topbar">
            <div class="topbar-left">
                <button class="toggle-sidebar" onclick="toggleSidebar()">☰</button>
                <div class="topbar-title">LP3I Purwakarta &mdash; Penjaminan Mutu Akademik</div>
            </div>

            <div class="topbar-right">
                <!-- DARK / LIGHT MODE TOGGLE -->
                <button type="button" class="btn-theme-toggle" onclick="toggleTheme()" title="Ganti Mode Gelap / Terang">
                    <span class="theme-icon-light">☀️</span>
                    <span class="theme-icon-dark">🌙</span>
                </button>

                <div class="topbar-user" onclick="location.href='{{ route('profile') }}'">
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">{{ auth()->user()->role }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" style="margin-left: 6px;" onclick="event.stopPropagation();">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm" title="Logout">Keluar 🚪</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✓ {{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <span>✕ {{ session('error') }}</span>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <span>⚠️ {{ session('warning') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- SCRIPT UTILITIES -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) sidebar.classList.toggle('mobile-open');
            if (overlay) overlay.classList.toggle('active');
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.add('active');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) modal.classList.remove('active');
        }

        // Close modal when clicking outside
        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('lp3i_theme', newTheme);
        }
    </script>
    @stack('scripts')
</body>
</html>
