@extends('admin.layouts.app')

@section('title', 'Editar projeto')

@section('topbar-actions')
    <a href="{{ route('admin.projects.files.index', $project) }}" class="admin-btn"><i class="fa-solid fa-file-arrow-up"></i> Arquivos</a>
@endsection

@section('content')

    <div class="admin-card" style="max-width:760px">
        <form method="POST" action="{{ route('admin.projects.update', $project) }}">
            @csrf @method('PUT')
            @include('admin.projects._form')
            <div style="display:flex;gap:10px;margin-top:8px">
                <button type="submit" class="admin-btn admin-btn-primary"><i class="fa-solid fa-check"></i> Salvar</button>
                <a href="{{ route('admin.projects.index') }}" class="admin-btn">Voltar</a>
            </div>
        </form>
    </div>

@endsection
