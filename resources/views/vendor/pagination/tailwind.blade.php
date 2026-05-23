@if ($paginator->hasPages())
<div class="flex items-center gap-1">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
    <span class="px-3 py-1.5 text-xs text-ink-faint border border-surface-border rounded-lg cursor-not-allowed">
        ← Sebelumnya
    </span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-xs font-semibold text-ink-muted border border-surface-border rounded-lg hover:bg-slate-50 hover:text-primary transition-colors">
        ← Sebelumnya
    </a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
        <span class="px-2.5 py-1.5 text-xs text-ink-faint">{{ $element }}</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
            <span class="px-3 py-1.5 text-xs font-bold bg-primary text-white rounded-lg">{{ $page }}</span>
            @else
            <a href="{{ $url }}" class="px-3 py-1.5 text-xs font-semibold text-ink-muted border border-surface-border rounded-lg hover:bg-slate-50 hover:text-primary transition-colors">
                {{ $page }}
            </a>
            @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-xs font-semibold text-ink-muted border border-surface-border rounded-lg hover:bg-slate-50 hover:text-primary transition-colors">
        Selanjutnya →
    </a>
    @else
    <span class="px-3 py-1.5 text-xs text-ink-faint border border-surface-border rounded-lg cursor-not-allowed">
        Selanjutnya →
    </span>
    @endif
</div>
@endif
