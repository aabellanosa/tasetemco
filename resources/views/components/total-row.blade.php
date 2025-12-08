@props(['label','code','value','formula'=>null])

<div class="row my-2 align-items-center fw-bold bg-light py-2 border-top border-bottom">
    <div class="col-6">
        {{ $label }}
    </div>
    <div class="col-6 text-end">
        <span 
            class="derived-value" 
            data-code="{{ $code }}"
            @if($formula)
                data-formula="{{ $formula }}"
            @endif
        >
            {{ number_format($value, 2) }}
        </span>
    </div>
</div>
