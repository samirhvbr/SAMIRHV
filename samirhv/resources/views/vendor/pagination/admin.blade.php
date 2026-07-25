{{-- Paginação do painel.

     Substitui a view padrão do Laravel (pagination::tailwind), que traz classes
     utilitárias e SVGs dimensionados por Tailwind — como o admin NÃO carrega
     Tailwind, aquele `<svg class="w-5 h-5">` ficava sem tamanho e renderizava do
     tamanho do container (o "chevron gigante"). Aqui é HTML simples, em pt-BR e
     com os tokens do painel. Registrada em AppServiceProvider::useAdminPagination(). --}}
@include('vendor.pagination._style')

@if ($paginator->hasPages())
    <nav class="pg" role="navigation" aria-label="Paginação">
        <p class="pg__info">
            @if($paginator->total() > 0)
                <b>{{ $paginator->firstItem() }}</b>–<b>{{ $paginator->lastItem() }}</b> de <b>{{ number_format($paginator->total(), 0, ',', '.') }}</b>
            @else
                nenhum resultado
            @endif
        </p>

        <div class="pg__links">
            @if ($paginator->onFirstPage())
                <span class="is-disabled" aria-disabled="true">← anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev">← anterior</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pg__gap pg__num" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="is-current pg__num" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pg__num" aria-label="Página {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next">próxima →</a>
            @else
                <span class="is-disabled" aria-disabled="true">próxima →</span>
            @endif
        </div>
    </nav>
@endif
