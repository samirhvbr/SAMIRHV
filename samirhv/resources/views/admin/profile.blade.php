@extends('admin.layouts.app')

@section('title', 'Perfil')

@section('content')

    @if($user->must_change_password)
        <div class="admin-alert admin-alert-warn">Por segurança, defina uma nova senha antes de continuar.</div>
    @endif

    <div class="admin-card" style="max-width:520px">
        <h2>Trocar senha</h2>
        <div class="card-sub" style="margin-bottom:18px">{{ $user->email }}</div>

        <form method="POST" action="{{ route('admin.profile.password') }}">
            @csrf
            <div class="form-row">
                <label for="current_password">Senha atual</label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                @error('current_password')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <label for="password">Nova senha</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
                <div class="hint">Mínimo de 10 caracteres.</div>
                @error('password')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="form-row">
                <label for="password_confirmation">Confirmar nova senha</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>
            <button type="submit" class="admin-btn admin-btn-primary"><i class="fa-solid fa-check"></i> Atualizar senha</button>
        </form>
    </div>

@endsection
