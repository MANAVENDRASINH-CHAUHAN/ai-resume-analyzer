@php
    $links = [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'match' => 'admin.dashboard',
            'icon' => 'bi-grid',
        ],
        [
            'label' => 'Job Roles',
            'route' => 'admin.job-roles.index',
            'match' => 'admin.job-roles.*',
            'icon' => 'bi-briefcase',
        ],
        [
            'label' => 'Resumes',
            'route' => 'admin.resumes.index',
            'match' => 'admin.resumes.*',
            'icon' => 'bi-file-earmark-richtext',
        ],
        [
            'label' => 'Reports',
            'route' => 'admin.reports.index',
            'match' => 'admin.reports.*',
            'icon' => 'bi-bar-chart-line',
        ],
    ];
@endphp

<aside class="admin-sidebar">
    <div class="sidebar-user-card mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="sidebar-user-icon">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                <div class="small text-white-50">Administrator</div>
            </div>
        </div>
    </div>

    <nav class="nav flex-column gap-2">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}" class="sidebar-link {{ request()->routeIs($link['match']) ? 'active' : '' }}">
                <i class="bi {{ $link['icon'] }}"></i>
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach
    </nav>
</aside>
