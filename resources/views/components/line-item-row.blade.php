<div class="row mb-2 align-items-center">
    <div class="col-6">
        {{ $label }}
    </div>
    <div class="col-6 text-end">
        <input type="number" 
               step="0.01"
               name="{{ $code }}"
               class="form-control text-end value-input"
               data-code="{{ $code }}"
               value="{{ $value }}">
    </div>
</div>
