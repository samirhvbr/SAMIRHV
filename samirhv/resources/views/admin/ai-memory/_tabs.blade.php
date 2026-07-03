@php $r = (string) request()->route()?->getName(); @endphp
<div class="tabs">
    <a href="{{ route('admin.ai-memory.dashboard') }}" class="admin-btn admin-btn-sm {{ $r === 'admin.ai-memory.dashboard' ? 'admin-btn-primary' : '' }}">Dashboard</a>
    <a href="{{ route('admin.ai-memory.projects') }}" class="admin-btn admin-btn-sm {{ str_starts_with($r, 'admin.ai-memory.projects') ? 'admin-btn-primary' : '' }}">Projetos</a>
    <a href="{{ route('admin.ai-memory.pages') }}" class="admin-btn admin-btn-sm {{ str_starts_with($r, 'admin.ai-memory.pages') ? 'admin-btn-primary' : '' }}">Páginas</a>
    <a href="{{ route('admin.ai-memory.sessions') }}" class="admin-btn admin-btn-sm {{ str_starts_with($r, 'admin.ai-memory.sessions') ? 'admin-btn-primary' : '' }}">Sessões</a>
    <a href="{{ route('admin.ai-memory.observations') }}" class="admin-btn admin-btn-sm {{ str_starts_with($r, 'admin.ai-memory.observations') ? 'admin-btn-primary' : '' }}">Observações</a>
    <a href="{{ route('admin.ai-memory.handoffs') }}" class="admin-btn admin-btn-sm {{ str_starts_with($r, 'admin.ai-memory.handoffs') ? 'admin-btn-primary' : '' }}">Handoffs</a>
    <a href="{{ route('admin.ai-memory.search') }}" class="admin-btn admin-btn-sm {{ str_starts_with($r, 'admin.ai-memory.search') ? 'admin-btn-primary' : '' }}">Busca</a>
</div>
