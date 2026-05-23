@if ($paginator->hasPages())
<div class="flex items-center gap-2">
    @if ($paginator->onFirstPage())
    <span class="px-4 py-1.5 text-xs text-ink-faint border border-surface-border rounded-lg cursor-not-allowed">← Sebelumnya</span>
    @else
    <a href="{{ $paginator->previousPageUrl() }}" class="px-4 py-1.5 text-xs font-semibold text-ink-muted border border-surface-border rounded-lg hover:bg-slate-50 hover:text-primary transition-colors">← Sebelumnya</a>
    @endif

    @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" class="px-4 py-1.5 text-xs font-semibold text-ink-muted border border-surface-border rounded-lg hover:bg-slate-50 hover:text-primary transition-colors">Selanjutnya →</a>
    @else
    <span class="px-4 py-1.5 text-xs text-ink-faint border border-surface-border rounded-lg cursor-not-allowed">Selanjutnya →</span>
    @endif
</div>
@endif
