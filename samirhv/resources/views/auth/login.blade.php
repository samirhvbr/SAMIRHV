<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Entrar — Samirhv</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: #0d0d14; color: #e2e8f0; padding: 24px;
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        }
        .login-card {
            width: 100%; max-width: 400px; background: #14141f;
            border: 1px solid rgba(99,102,241,0.18); border-radius: 16px; padding: 38px 34px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .brand { font-family: 'JetBrains Mono', monospace; font-size: 1.6rem; font-weight: 700; letter-spacing: -0.02em; color: #f1f5f9; margin-bottom: 4px; }
        .brand span { color: #6366f1; }
        .subtitle { color: #94a3b8; font-size: 0.9rem; margin-bottom: 28px; }
        label { display: block; font-size: 0.82rem; color: #cbd5e1; margin-bottom: 7px; font-weight: 600; }
        input[type=email], input[type=password] {
            width: 100%; background: #0d0d14; border: 1px solid rgba(99,102,241,0.22); color: #e2e8f0;
            border-radius: 9px; padding: 12px 14px; font-size: 0.95rem; margin-bottom: 18px; outline: none;
        }
        input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.18); }
        .remember { display: flex; align-items: center; gap: 8px; margin-bottom: 22px; font-size: 0.85rem; color: #94a3b8; }
        .remember input { margin: 0; }
        button {
            width: 100%; background: #6366f1; border: none; color: #fff; font-weight: 600; font-size: 0.95rem;
            border-radius: 9px; padding: 13px; cursor: pointer; transition: background .15s ease;
        }
        button:hover { background: #4f46e5; }
        .error-box { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; border-radius: 9px; padding: 11px 14px; font-size: 0.85rem; margin-bottom: 20px; }
        .field-error { color: #fca5a5; font-size: 0.78rem; margin-top: -12px; margin-bottom: 16px; }
        .back { display: block; text-align: center; margin-top: 22px; color: #64748b; font-size: 0.82rem; text-decoration: none; }
        .back:hover { color: #94a3b8; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand">samirhv<span>.</span></div>
        <div class="subtitle">Painel administrativo</div>

        @if(session('error'))
            <div class="error-box">{{ session('error') }}</div>
        @endif
        @if($errors->has('email'))
            <div class="error-box">{{ $errors->first('email') }}</div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">

            <label for="password">Senha</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <label class="remember">
                <input type="checkbox" name="remember" value="1"> Manter conectado
            </label>

            <button type="submit">Entrar</button>
        </form>

        <a href="{{ route('home') }}" class="back">← Voltar ao site</a>
    </div>
</body>
</html>
