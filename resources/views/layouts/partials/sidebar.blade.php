<nav class="d-flex flex-column py-2">
    @php $menu = $menu ?? []; @endphp
    @forelse ($menu as $module)
        @if (count($module['submodules'] ?? []) > 0)
            <div class="nav-header">{{ $module['label'] }}</div>
            @foreach ($module['submodules'] as $sub)
                @if (!$sub['url']) @continue @endif
                <a href="{{ $sub['url'] }}"
                   class="nav-link {{ $sub['route_name'] && request()->routeIs($sub['route_name'] . '*') ? 'active' : '' }}"
                   title="{{ $sub['label'] }}">
                    @if ($sub['icon'])<i class="bi {{ $sub['icon'] }}"></i>@else<i class="bi bi-dot"></i>@endif
                    <span>{!! $sub['label'] !!}</span>
                </a>
            @endforeach
        @elseif ($module['url'])
            <a href="{{ $module['url'] }}"
               class="nav-link {{ $module['route_name'] && request()->routeIs($module['route_name'] . '*') ? 'active' : '' }}"
               title="{{ $module['label'] }}">
                @if ($module['icon'])<i class="bi {{ $module['icon'] }}"></i>@else<i class="bi bi-dot"></i>@endif
                <span>{{ $module['label'] }}</span>
            </a>
        @endif
    @empty
        <a href="{{ route('dashboard') }}" class="nav-link"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
    @endforelse
</nav>
