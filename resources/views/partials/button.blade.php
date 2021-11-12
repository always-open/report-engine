<button
    @if($class ?? false) class="{{ $class }}" @endif
    @if($href ?? false) onclick="window.location='{{ $href }}'"
    @elseif($function ?? false) onclick="{{ $function }}"
    @endif>
    {{ $label ?? $href }}
</button>
