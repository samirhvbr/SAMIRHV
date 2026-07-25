{{-- Paginação "simples" (só anterior/próxima) — mesmo motivo e mesmo visual da
     admin.blade.php ao lado. Usada por simplePaginate(). --}}
@include('vendor.pagination._style')

@if ($paginator->hasPages())
    <nav class="pg" role="navigation" aria-label="Paginação">
        <p class="pg__info">página <b>{{ $paginator->currentPage() }}</b></p>
        <div class="pg__links">
            @if ($paginator->onFirstPage())
                <span class="is-disabled" aria-disabled="true">← anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev">← anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next">próxima →</a>
            @else
                <span class="is-disabled" aria-disabled="true">próxima →</span>
            @endif
        </div>
    </nav>
@endif
