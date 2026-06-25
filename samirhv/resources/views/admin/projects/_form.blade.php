{{-- Campos compartilhados entre criar e editar projeto. Espera $project. --}}
<div class="form-row">
    <label for="title">Título *</label>
    <input type="text" id="title" name="title" value="{{ old('title', $project->title) }}" required maxlength="255">
    @error('title')<div class="err">{{ $message }}</div>@enderror
</div>

<div class="form-row">
    <label for="slug">Slug (URL)</label>
    <input type="text" id="slug" name="slug" value="{{ old('slug', $project->slug) }}" maxlength="255" placeholder="gerado a partir do título se vazio">
    <div class="hint">URL pública: /p/<strong>{{ old('slug', $project->slug) ?: 'slug' }}</strong> — letras, números, hífen e underscore.</div>
    @error('slug')<div class="err">{{ $message }}</div>@enderror
</div>

<div class="form-row">
    <label for="description">Descrição</label>
    <textarea id="description" name="description" maxlength="5000">{{ old('description', $project->description) }}</textarea>
    @error('description')<div class="err">{{ $message }}</div>@enderror
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
    <div class="form-row">
        <label for="category">Categoria</label>
        <input type="text" id="category" name="category" value="{{ old('category', $project->category) }}" maxlength="60" placeholder="ex: Desktop, CLI, Linux">
        @error('category')<div class="err">{{ $message }}</div>@enderror
    </div>
    <div class="form-row">
        <label for="icon">Ícone (Font Awesome)</label>
        <input type="text" id="icon" name="icon" value="{{ old('icon', $project->icon) }}" maxlength="60" placeholder="ex: fa-solid fa-terminal">
        @error('icon')<div class="err">{{ $message }}</div>@enderror
    </div>
    <div class="form-row">
        <label for="sort_order">Ordem</label>
        <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $project->sort_order ?? 0) }}" min="0" max="9999">
        @error('sort_order')<div class="err">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-row">
    <label class="form-check">
        <input type="hidden" name="is_published" value="0">
        <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $project->is_published ?? true))>
        Publicado (visível no site)
    </label>
</div>
