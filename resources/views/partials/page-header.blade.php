@php
    $breadcrumbs = $breadcrumbs ?? [];
    $badge = $badge ?? null;
@endphp

<div class="page-header-card mb-4">
    @if (! empty($breadcrumbs))
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                @foreach ($breadcrumbs as $breadcrumb)
                    <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}" @if($loop->last) aria-current="page" @endif>
                        @if (! $loop->last && ! empty($breadcrumb['url']))
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['label'] }}</a>
                        @else
                            {{ $breadcrumb['label'] }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="page-title mb-1">{{ $title ?? 'Page Title' }}</h1>
            <p class="text-muted mb-0">{{ $subtitle ?? 'Page subtitle goes here.' }}</p>
        </div>

        @if ($badge)
            <span class="badge text-bg-light border rounded-pill px-3 py-2">{{ $badge }}</span>
        @endif
    </div>
</div>
