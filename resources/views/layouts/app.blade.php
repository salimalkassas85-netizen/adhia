<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'هدية عيد الأضحى' }}</title>
    <style>
        :root {
            --bg: #fbfaf7;
            --surface: #fff;
            --ink: #1f2a24;
            --muted: #667067;
            --line: #e7dfd0;
            --accent: #0f766e;
            --accent-dark: #0b5953;
            --gold: #b7791f;
            --gold-soft: #fff3d7;
            --danger: #b42318;
            --ok: #15803d;
            --soft: #edf7f5;
            --shadow: 0 14px 35px rgba(31, 42, 36, .08);
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: radial-gradient(circle at 15% 0%, #fff7e8 0, transparent 32%), var(--bg);
            color: var(--ink);
            font-family: Tahoma, Arial, sans-serif;
            line-height: 1.75;
        }
        a { color: var(--accent); text-decoration: none; }
        label { display: block; font-weight: 700; margin-bottom: 6px; }
        .site { min-height: 100vh; display: flex; flex-direction: column; }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(255, 255, 255, .92);
            border-bottom: 1px solid var(--line);
            backdrop-filter: blur(14px);
        }
        .nav {
            max-width: 1120px;
            margin: auto;
            padding: 12px 18px;
            display: flex;
            gap: 22px;
            align-items: center;
            justify-content: space-between;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            min-width: max-content;
            color: var(--ink);
            font-weight: 900;
            letter-spacing: 0;
        }
        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: linear-gradient(145deg, var(--accent), #159c8f);
            color: #fff;
            box-shadow: 0 8px 18px rgba(15, 118, 110, .22);
        }
        .brand-mark svg { width: 22px; height: 22px; }
        .navlinks {
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        .navlinks a:not(.btn), .navlinks button.linklike {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 8px;
            color: var(--ink);
            font-weight: 700;
            transition: background .2s ease, color .2s ease, transform .2s ease;
        }
        .navlinks a:not(.btn):hover, .navlinks button.linklike:hover {
            background: var(--soft);
            color: var(--accent-dark);
            transform: translateY(-1px);
        }
        .navlinks form { margin: 0; }

        .wrap { max-width: 1120px; margin: auto; width: 100%; padding: 30px 18px; }
        .hero { display: grid; gap: 28px; grid-template-columns: 1.08fr .92fr; align-items: center; padding: 38px 0; }
        .hero h1 { font-size: clamp(30px, 5vw, 54px); line-height: 1.2; margin: 0 0 12px; }
        .lead { font-size: 18px; color: var(--muted); max-width: 720px; }
        .panel, .card { background: var(--surface); border: 1px solid var(--line); border-radius: 8px; padding: 18px; box-shadow: var(--shadow); }
        .grid { display: grid; gap: 14px; }
        .grid-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-top: 14px; }
        .btn, button {
            border: 0;
            border-radius: 8px;
            background: var(--accent);
            color: #fff;
            font-weight: 800;
            padding: 10px 15px;
            cursor: pointer;
            font-family: inherit;
            line-height: 1.4;
        }
        .btn.secondary { background: #fff; color: var(--accent); border: 1px solid var(--accent); }
        .btn.gold { background: var(--gold); }
        .btn.danger { background: var(--danger); }
        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            padding: 10px 12px;
            font-family: inherit;
            font-size: 15px;
        }
        textarea { min-height: 100px; }
        .field { margin-bottom: 14px; }
        .hint, .errors { color: var(--muted); font-size: 14px; }
        .notice { background: var(--soft); border: 1px solid #b8ded8; border-radius: 8px; padding: 12px; color: #23534c; }
        .errors { background: #fff2f0; border: 1px solid #ffd3cc; color: var(--danger); border-radius: 8px; padding: 12px; margin-bottom: 14px; }
        .map { height: 310px; border: 1px solid var(--line); border-radius: 8px; background: #e9eee9; overflow: hidden; }
        .table { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
        .table th, .table td { padding: 10px; border-bottom: 1px solid var(--line); text-align: right; vertical-align: top; }
        .badge { display: inline-flex; align-items: center; border-radius: 999px; padding: 3px 9px; font-size: 13px; background: #f2eee4; color: #59451f; }
        .badge.ok { background: #e7f7ec; color: var(--ok); }
        .badge.warn { background: #fff7df; color: #8a5a00; }
        .badge.danger { background: #fff2f0; color: var(--danger); }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        .stat strong { display: block; font-size: 26px; }
        .auth-shell { max-width: 520px; margin: 40px auto; }
        .footer { margin-top: auto; border-top: 1px solid var(--line); color: var(--muted); padding: 22px 18px; text-align: center; }
        .privacy { font-size: 14px; color: var(--muted); }

        .giving-animation {
            position: relative;
            min-height: 320px;
            overflow: hidden;
            background: linear-gradient(160deg, #ffffff 0%, #fff8ea 52%, #eef8f6 100%);
        }
        .giving-orbit {
            position: absolute;
            inset: 28px;
            border: 1px dashed rgba(15, 118, 110, .25);
            border-radius: 999px;
            animation: orbitPulse 4.8s ease-in-out infinite;
        }
        .giving-icon {
            position: absolute;
            width: 74px;
            height: 74px;
            border-radius: 22px;
            display: grid;
            place-items: center;
            color: #fff;
            background: var(--accent);
            box-shadow: 0 16px 30px rgba(15, 118, 110, .2);
            animation: floatGift 3.8s ease-in-out infinite;
        }
        .giving-icon svg { width: 38px; height: 38px; }
        .giving-icon.donor { top: 42px; right: 38px; background: var(--gold); animation-delay: -.6s; }
        .giving-icon.team { top: 124px; left: 50%; transform: translateX(-50%); }
        .giving-icon.family { bottom: 42px; left: 38px; background: #275342; animation-delay: -.2s; }
        .giving-path {
            position: absolute;
            top: 102px;
            left: 76px;
            right: 76px;
            height: 120px;
            border-top: 3px solid rgba(183, 121, 31, .35);
            border-radius: 50%;
        }
        .giving-dot {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--gold);
            top: 96px;
            right: 94px;
            animation: moveDonation 3.8s ease-in-out infinite;
        }
        .giving-caption {
            position: absolute;
            right: 18px;
            left: 18px;
            bottom: 18px;
            padding: 12px;
            background: rgba(255, 255, 255, .78);
            border: 1px solid rgba(231, 223, 208, .8);
            border-radius: 8px;
            color: var(--muted);
            font-weight: 700;
            text-align: center;
        }
        @keyframes floatGift {
            0%, 100% { margin-top: 0; }
            50% { margin-top: -10px; }
        }
        @keyframes moveDonation {
            0% { right: 94px; opacity: 0; transform: scale(.7); }
            18% { opacity: 1; }
            52% { right: calc(50% - 6px); transform: scale(1); }
            88% { right: calc(100% - 106px); opacity: 1; }
            100% { right: calc(100% - 106px); opacity: 0; transform: scale(.7); }
        }
        @keyframes orbitPulse {
            0%, 100% { transform: scale(1); opacity: .75; }
            50% { transform: scale(.96); opacity: .45; }
        }
        @media (prefers-reduced-motion: reduce) {
            .giving-icon, .giving-dot, .giving-orbit { animation: none; }
        }
        @media (max-width: 760px) {
            .hero, .grid-2, .grid-3, .stats { grid-template-columns: 1fr; }
            .nav { align-items: stretch; flex-direction: column; gap: 12px; }
            .navlinks { justify-content: flex-start; gap: 8px; }
            .navlinks a:not(.btn), .navlinks button.linklike { background: #fff; border: 1px solid var(--line); }
            .table { font-size: 14px; }
            .wrap { padding: 20px 14px; }
            .giving-animation { min-height: 280px; }
        }
    </style>
    @stack('head')
</head>
<body>
<div class="site">
    <header class="topbar">
        <nav class="nav">
            <a class="brand" href="{{ route('home') }}" aria-label="هدية عيد الأضحى">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M9 13h6M12 10v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <span>هدية عيد الأضحى</span>
            </a>
            <div class="navlinks">
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">لوحة الإدارة</a>
                        <a href="{{ route('admin.beneficiary-requests.index') }}">طلبات الهدية</a>
                        <a href="{{ route('admin.donations.index') }}">المساهمات</a>
                        <a href="{{ route('admin.agents.index') }}">فريق التوزيع</a>
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.admin-users.index') }}">أدمنز المناطق</a>
                            <a href="{{ route('admin.areas.index') }}">المناطق</a>
                        @endif
                    @else
                        <a href="{{ route('agent.dashboard') }}">لوحة التوزيع</a>
                        <a href="{{ route('agent.requests.index') }}">طلبات مسندة</a>
                    @endif
                    <form method="post" action="{{ route('logout') }}">@csrf<button class="btn secondary" type="submit">خروج</button></form>
                @else
                    <a href="{{ route('public.request.create') }}">طلب هدية العيد</a>
                    <a href="{{ route('public.donation.create') }}">المساهمة</a>
                    <a href="{{ route('login') }}">دخول الفريق</a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="wrap">
        @if (session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <footer class="footer">
        <div>الأمانة والستر وحفظ الخصوصية أساس هذه المبادرة.</div>
        @auth
            <div class="privacy">
                مسجل الدخول: <strong>{{ auth()->user()->name }}</strong>
                -
                <strong>{{ auth()->user()->roleLabel() }}</strong>
                @if(auth()->user()->area)
                    - {{ auth()->user()->area->name }}
                @endif
            </div>
        @endauth
    </footer>
</div>
@stack('scripts')
</body>
</html>
