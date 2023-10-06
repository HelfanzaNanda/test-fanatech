<div class="form-group w-100">
    <label class="text-capitalize">{{ $label }}</label>
    <input {{ $attributes->merge(['class' => 'form-control']) }} type="{{ $type ?? "text" }}" name="{{ $name }}" id="input-{{ $name }}">
</div>
