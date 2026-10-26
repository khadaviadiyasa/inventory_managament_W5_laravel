{{-- $options: array [value => label] --}}
@props(['name', 'label', 'options' => [], 'value' => null, 'placeholder' => null])
<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}" {{ $attributes->class(['is-invalid' => $errors->has($name)]) }}>
        @if($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $optValue => $optLabel)
            <option value="{{ $optValue }}" @selected((string) old($name, $value) === (string) $optValue)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @error($name)
        <small class="error">{{ $message }}</small>
    @enderror
</div>
