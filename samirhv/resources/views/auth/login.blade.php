<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Entrar — Samirhv</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap');
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            background: #0b0b11; color: #c3c8d8; padding: 24px;
            font-family: 'Archivo', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        .login-card {
            width: 100%; max-width: 400px; background: #14141e;
            border: 1px solid rgba(150,155,185,0.14); border-radius: 16px; padding: 38px 34px;
            box-shadow: 0 30px 80px -30px rgba(0,0,0,0.85);
        }
        .brand { font-family: 'JetBrains Mono', monospace; font-size: 1.6rem; font-weight: 700; letter-spacing: -0.02em; color: #f4f5fb; margin-bottom: 4px; }
        .brand span { color: #6366f1; }
        .subtitle { color: #8b93a7; font-size: 0.9rem; margin-bottom: 28px; }
        label { display: block; font-size: 0.82rem; color: #c3c8d8; margin-bottom: 7px; font-weight: 600; }
        input[type=email], input[type=password] {
            width: 100%; background: #0b0b11; border: 1px solid rgba(150,155,185,0.16); color: #f4f5fb;
            border-radius: 9px; padding: 12px 14px; font-size: 0.95rem; margin-bottom: 18px; outline: none;
            font-family: inherit;
        }
        input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.2); }
        .remember { display: flex; align-items: center; gap: 8px; margin-bottom: 22px; font-size: 0.85rem; color: #8b93a7; }
        .remember input { margin: 0; accent-color: #6366f1; }
        button {
            width: 100%; background: #6366f1; border: none; color: #fff; font-weight: 600; font-size: 0.95rem;
            border-radius: 9px; padding: 13px; cursor: pointer; transition: background .18s ease; font-family: inherit;
        }
        button:hover { background: #4f46e5; }
        .error-box { background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); color: #fca5a5; border-radius: 9px; padding: 11px 14px; font-size: 0.85rem; margin-bottom: 20px; }
        .field-error { color: #fca5a5; font-size: 0.78rem; margin-top: -12px; margin-bottom: 16px; }
        .back { display: block; text-align: center; margin-top: 22px; color: #8b93a7; font-size: 0.82rem; text-decoration: none; }
        .back:hover { color: #a5b4fc; }
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
