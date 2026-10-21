{{-- Styled native select: keeps keyboard/a11y behaviour, replaces the browser chrome. --}}
<div class="relative {{ $attributes->get('wrapper-class') }}">
    <select {{ $attributes->except('wrapper-class')->merge(['class' => 'select']) }}>{{ $slot }}</select>
    <x-icon name="chevron-down" class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
</div>
