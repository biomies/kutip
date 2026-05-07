@if ($paginator->hasPages())
<nav class="pagination">
    @if ($paginator->onFirstPage())
        <span class="page-btn page-btn--disabled">←</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="page-btn">←</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="page-btn page-btn--disabled">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="page-btn page-btn--current">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="page-btn">→</a>
    @else
        <span class="page-btn page-btn--disabled">→</span>
    @endif
</nav>
@endif
