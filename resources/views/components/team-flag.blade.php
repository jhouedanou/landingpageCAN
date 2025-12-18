@props(['isoCode', 'teamName', 'size' => 'medium'])

@php
    // Define sizes
    $sizes = [
        'small' => ['container' => 'w-5 h-4', 'text' => 'text-[8px]'],
        'medium' => ['container' => 'w-8 h-6', 'text' => 'text-xs'],
        'large' => ['container' => 'w-16 h-12', 'text' => 'text-sm'],
        'xlarge' => ['container' => 'w-20 h-20', 'text' => '2xl'],
    ];

    $sizeClasses = $sizes[$size] ?? $sizes['medium'];
    $containerClass = $sizeClasses['container'];
    $textClass = $sizeClasses['text'];

    // Generate fallback initials
    $initials = mb_substr($isoCode ?? $teamName ?? '??', 0, 2);
@endphp

<div class="flag-container inline-block {{ $containerClass }}">
    @if($isoCode)
        <img src="https://flagcdn.com/w80/{{ $isoCode }}.png"
             alt="{{ $teamName }}"
             class="{{ $containerClass }} object-cover rounded shadow"
             onerror="this.parentElement.innerHTML='<div class=\'{{ $containerClass }} bg-gradient-to-br from-soboa-blue to-blue-600 rounded shadow flex items-center justify-center\'><span class=\'text-white {{ $textClass }} font-black\'>{{ $initials }}</span></div>'">
    @else
        <div class="{{ $containerClass }} bg-gradient-to-br from-soboa-blue to-blue-600 rounded shadow flex items-center justify-center">
            <span class="text-white {{ $textClass }} font-black">{{ $initials }}</span>
        </div>
    @endif
</div>
