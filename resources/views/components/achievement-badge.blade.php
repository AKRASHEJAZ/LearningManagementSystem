@php
    $tierClass = match ($tier) {
        'platinum' => 'border-primary',
        'gold' => 'border-warning',
        'silver' => 'border-secondary',
        default => 'border-dark-subtle',
    };

    $bg = match ($tier) {
        'platinum' => 'bg-primary-subtle',
        'gold' => 'bg-warning-subtle',
        'silver' => 'bg-secondary-subtle',
        default => 'bg-light',
    };

    $iconBg = $locked ? 'bg-light' : $bg;
@endphp

<div class="card shadow-sm h-100 {{ $tierClass }} @if($locked) opacity-75 @endif" style="border-width:2px;">
    <div class="card-body">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center {{ $iconBg }}" style="width: 44px; height: 44px; border: 1px solid rgba(0,0,0,.08);">
                @switch($icon)
                    @case('shield')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 2l7 4v6c0 5-3.5 9.5-7 10-3.5-.5-7-5-7-10V6l7-4z" stroke="currentColor" stroke-width="2"/>
                            <path d="M9.5 12l1.8 1.8L15.5 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        @break
                    @case('trophy')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8 4h8v4a4 4 0 0 1-8 0V4z" stroke="currentColor" stroke-width="2"/>
                            <path d="M6 6H4a2 2 0 0 0 2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M18 6h2a2 2 0 0 1-2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M12 12v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M9 20h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10 15h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        @break
                    @case('medal')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M8 2h3l1 3 1-3h3l-2 6H10L8 2z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <circle cx="12" cy="16" r="5" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 13l1 2h2l-1.6 1.2.6 2L12 17l-2 1.2.6-2L9 15h2l1-2z" fill="currentColor"/>
                        </svg>
                        @break
                    @case('crown')
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 8l4 4 4-6 4 6 4-4v10H4V8z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        @break
                    @default
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 2l3 6 7 .7-5 4.2 1.6 7-6.6-3.7L5.4 20 7 13 2 8.7 9 8l3-6z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                @endswitch
            </div>

            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="fw-semibold">{{ $name }}</div>
                    <span class="badge text-bg-light border">{{ $points }} XP</span>
                </div>
                @if($description)
                    <div class="text-secondary small mt-1">{{ $description }}</div>
                @endif

                <div class="mt-2 small">
                    @if($locked)
                        <span class="badge text-bg-light border">Locked</span>
                    @elseif($earnedAt)
                        <span class="badge text-bg-success">Unlocked</span>
                        <span class="text-secondary ms-1">· {{ $earnedAt }}</span>
                    @else
                        <span class="badge text-bg-success">Unlocked</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

