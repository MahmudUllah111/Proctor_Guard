{{-- Square 32x32 micro-action. Renders <a> when href is given, otherwise <button>. Label doubles as native tooltip + aria-label. --}}
@props(['icon', 'label', 'href' => null, 'tone' => 'default', 'type' => 'button'])
@if($href)
    <a href="{{ $href }}" title="{{ $label }}" aria-label="{{ $label }}" data-tone="{{ $tone }}" {{ $attributes->merge(['class' => 'icon-btn']) }}>
        <x-icon :name="$icon" />
    </a>
@else
    <button type="{{ $type }}" title="{{ $label }}" aria-label="{{ $label }}" data-tone="{{ $tone }}" {{ $attributes->merge(['class' => 'icon-btn']) }}>
        <x-icon :name="$icon" />
    </button>
@endif
