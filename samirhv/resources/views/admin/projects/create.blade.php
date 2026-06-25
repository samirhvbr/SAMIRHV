@extends('admin.layouts.app')

@section('title', 'Novo projeto')

@section('content')

    <div class="admin-card" style="max-width:760px">
        <form method="POST" action="{{ route('admin.projects.store') }}">
            @csrf
            @include('admin.projects._form')
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="admin-btn admin-btn-primary"><i class="fa-solid fa-check"></i> Criar e enviar arquivos</button>
                <a href="{{ route('admin.projects.index') }}" class="admin-btn">Cancelar</a>
            </div>
        </form>
    </div>

@endsection
