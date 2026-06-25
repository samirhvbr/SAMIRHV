<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Painel') — Samirhv Admin</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('vendor/canvas/css/font-icons.css') }}">
    <style>
        :root{
            --bg:#0d0d14; --panel:#14141f; --panel-2:#11111c; --line:rgba(99,102,241,.15);
            --txt:#e2e8f0; --muted:#94a3b8; --dim:#64748b; --accent:#6366f1; --accent-soft:rgba(99,102,241,.12);
            --ok:#22c55e; --danger:#ef4444; --warn:#f59e0b;
        }
        *{box-sizing:border-box}
        body{margin:0;background:var(--bg);color:var(--txt);font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,sans-serif;font-size:14px}
        a{color:inherit}
        .layout{display:flex;min-height:100vh}
        /* Sidebar */
        .sidebar{width:236px;flex-shrink:0;background:var(--panel);border-right:1px solid var(--line);padding:22px 16px;position:sticky;top:0;height:100vh;display:flex;flex-direction:column}
        .sidebar .brand{font-family:'JetBrains Mono',monospace;font-size:1.35rem;font-weight:700;letter-spacing:-.02em;color:#f1f5f9;margin:4px 8px 24px}
        .sidebar .brand span{color:var(--accent)}
        .nav-link{display:flex;align-items:center;gap:11px;padding:10px 12px;border-radius:9px;color:var(--muted);text-decoration:none;font-weight:500;margin-bottom:3px}
        .nav-link i{width:18px;text-align:center;font-size:.95rem}
        .nav-link:hover{background:var(--accent-soft);color:#c7d2fe}
        .nav-link.active{background:var(--accent-soft);color:#c7d2fe}
        .sidebar .spacer{flex:1}
        .sidebar .who{font-size:.76rem;color:var(--dim);margin:0 8px 10px;word-break:break-all}
        /* Main */
        .main{flex:1;min-width:0;display:flex;flex-direction:column}
        .topbar{display:flex;align-items:center;justify-content:space-between;padding:18px 28px;border-bottom:1px solid var(--line)}
        .topbar h1{font-size:1.3rem;font-weight:700;margin:0;color:#f1f5f9}
        .content{padding:28px;max-width:1180px;width:100%}
        /* Alerts */
        .admin-alert{border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:.9rem}
        .admin-alert-ok{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#86efac}
        .admin-alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#fca5a5}
        .admin-alert-warn{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:#fcd34d}
        /* Cards */
        .admin-card{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:22px;margin-bottom:22px}
        .admin-card h2{font-size:1rem;font-weight:700;margin:0 0 16px;color:#f1f5f9}
        .admin-card .card-sub{font-size:.78rem;color:var(--dim);font-weight:500}
        /* Stats */
        .admin-stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
        .admin-stat{background:var(--panel);border:1px solid var(--line);border-radius:12px;padding:18px 20px}
        .admin-stat .label{font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:var(--dim);font-weight:600}
        .admin-stat .value{font-size:1.9rem;font-weight:700;color:#f1f5f9;margin-top:6px;font-family:'JetBrains Mono',monospace}
        /* Tables */
        .admin-table{width:100%;border-collapse:collapse;font-size:.85rem}
        .admin-table th{text-align:left;padding:10px 12px;color:var(--dim);font-weight:600;font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--line)}
        .admin-table td{padding:11px 12px;border-bottom:1px solid rgba(99,102,241,.07);vertical-align:middle}
        .admin-table tr:hover td{background:rgba(99,102,241,.04)}
        .admin-table code{font-family:'JetBrains Mono',monospace;font-size:.8rem;color:#a5b4fc}
        .admin-table .muted{color:var(--dim)}
        .an-bot-row td{opacity:.55}
        /* Badges */
        .badge{display:inline-block;font-size:.68rem;font-weight:600;padding:3px 9px;border-radius:5px;font-family:'JetBrains Mono',monospace}
        .badge-accent{background:var(--accent-soft);color:#c7d2fe;border:1px solid rgba(99,102,241,.25)}
        .badge-ok{background:rgba(34,197,94,.12);color:#86efac;border:1px solid rgba(34,197,94,.25)}
        .badge-danger{background:rgba(239,68,68,.12);color:#fca5a5;border:1px solid rgba(239,68,68,.25)}
        .badge-warn{background:rgba(245,158,11,.12);color:#fcd34d;border:1px solid rgba(245,158,11,.25)}
        .badge-muted{background:rgba(148,163,184,.12);color:#cbd5e1;border:1px solid rgba(148,163,184,.2)}
        /* Buttons */
        .admin-btn{display:inline-flex;align-items:center;gap:7px;background:var(--panel-2);border:1px solid var(--line);color:var(--txt);padding:8px 15px;border-radius:8px;font-size:.84rem;font-weight:600;text-decoration:none;cursor:pointer}
        .admin-btn:hover{border-color:rgba(99,102,241,.4)}
        .admin-btn-primary{background:var(--accent);border-color:var(--accent);color:#fff}
        .admin-btn-primary:hover{background:#4f46e5}
        .admin-btn-danger{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);color:#fca5a5}
        .admin-btn-danger:hover{background:rgba(239,68,68,.18)}
        .admin-btn-sm{padding:5px 11px;font-size:.78rem}
        /* Forms */
        .form-row{margin-bottom:18px}
        .form-row label{display:block;font-size:.82rem;font-weight:600;color:#cbd5e1;margin-bottom:7px}
        .form-row input[type=text],.form-row input[type=number],.form-row input[type=password],.form-row input[type=file],.form-row textarea,.form-row select{
            width:100%;background:var(--bg);border:1px solid rgba(99,102,241,.22);color:var(--txt);border-radius:8px;padding:10px 12px;font-size:.9rem;outline:none;font-family:inherit}
        .form-row textarea{min-height:120px;resize:vertical}
        .form-row input:focus,.form-row textarea:focus,.form-row select:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(99,102,241,.16)}
        .form-row .hint{font-size:.74rem;color:var(--dim);margin-top:6px}
        .form-row .err{color:#fca5a5;font-size:.78rem;margin-top:6px}
        .form-check{display:flex;align-items:center;gap:9px;font-size:.88rem;color:#cbd5e1}
        .filters{display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;margin-bottom:18px}
        .filters .form-row{margin:0;min-width:150px}
        /* Analytics charts (CSS puro) */
        .an-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px}
        .an-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
        .an-chart{display:flex;align-items:flex-end;gap:5px;height:120px;padding-top:8px}
        .an-bar{flex:1;background:linear-gradient(180deg,var(--accent),rgba(99,102,241,.35));border-radius:3px 3px 0 0;min-height:2px;position:relative}
        .an-bar.bot{background:linear-gradient(180deg,#f59e0b,rgba(245,158,11,.3))}
        .an-xlabels{display:flex;gap:5px;margin-top:6px}
        .an-xlabels span{flex:1;text-align:center;font-size:.6rem;color:var(--dim);font-family:'JetBrains Mono',monospace}
        .an-hbar-row{display:flex;align-items:center;gap:10px;margin-bottom:9px;font-size:.82rem}
        .an-hbar-row .lbl{width:90px;flex-shrink:0;color:var(--muted)}
        .an-hbar-track{flex:1;background:var(--panel-2);border-radius:5px;height:14px;overflow:hidden}
        .an-hbar{height:100%;background:linear-gradient(90deg,var(--accent),rgba(99,102,241,.4));border-radius:5px}
        .an-hbar-row .val{width:42px;text-align:right;font-family:'JetBrains Mono',monospace;color:var(--dim);font-size:.76rem}
        .an-list{list-style:none;margin:0;padding:0}
        .an-list li{display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid rgba(99,102,241,.07);font-size:.84rem}
        .an-list li:last-child{border-bottom:none}
        .an-list .total{font-family:'JetBrains Mono',monospace;color:#c7d2fe}
        .chips{display:flex;flex-wrap:wrap;gap:8px}
        .chip{font-family:'JetBrains Mono',monospace;font-size:.74rem;background:var(--panel-2);border:1px solid var(--line);border-radius:6px;padding:5px 10px;color:var(--muted);text-decoration:none}
        .chip b{color:#c7d2fe}
        .tabs{display:flex;gap:8px;margin-bottom:22px;flex-wrap:wrap}
        .pagination{margin-top:16px}
        .pagination a,.pagination span{color:var(--muted)}
        @media(max-width:900px){.admin-stats-grid,.an-grid-3,.an-grid-2{grid-template-columns:1fr 1fr}.sidebar{width:64px}.sidebar .brand,.nav-link span,.sidebar .who{display:none}}
        @media(max-width:560px){.admin-stats-grid,.an-grid-3,.an-grid-2{grid-template-columns:1fr}}
    </style>
    @stack('styles')
</head>
<body>
@php $r = request()->route()?->getName(); @endphp
<div class="layout">
    <aside class="sidebar">
        <div class="brand">samirhv<span>.</span></div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $r === 'admin.dashboard' ? 'active' : '' }}"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a>
        <a href="{{ route('admin.projects.index') }}" class="nav-link {{ str_starts_with((string) $r, 'admin.projects') ? 'active' : '' }}"><i class="fa-solid fa-folder-open"></i><span>Projetos</span></a>
        <a href="{{ route('admin.audit.index') }}" class="nav-link {{ $r === 'admin.audit.index' ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i><span>Auditoria</span></a>
        <a href="{{ route('admin.access-audit.index') }}" class="nav-link {{ $r === 'admin.access-audit.index' ? 'active' : '' }}"><i class="fa-solid fa-user-shield"></i><span>Aud. de Acesso</span></a>
        <a href="{{ route('admin.profile') }}" class="nav-link {{ $r === 'admin.profile' ? 'active' : '' }}"><i class="fa-solid fa-gear"></i><span>Perfil</span></a>
        <div class="spacer"></div>
        <div class="who">{{ auth()->user()?->email }}</div>
        <a href="{{ route('home') }}" class="nav-link" target="_blank"><i class="fa-solid fa-up-right-from-square"></i><span>Ver site</span></a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link" style="width:100%;background:none;border:none;text-align:left;cursor:pointer;font:inherit"><i class="fa-solid fa-arrow-right-from-bracket"></i><span>Sair</span></button>
        </form>
    </aside>

    <main class="main">
        <div class="topbar">
            <h1>@yield('title', 'Painel')</h1>
            <div>@yield('topbar-actions')</div>
        </div>
        <div class="content">
            @if(session('status'))
                <div class="admin-alert admin-alert-ok">{{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="admin-alert admin-alert-error">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>
