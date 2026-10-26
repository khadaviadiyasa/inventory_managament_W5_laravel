@props(['name', 'label', 'value' => null, 'rows' => 3])
<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}"
        {{ $attributes->class(['is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <small class="error">{{ $message }}</small>
    @enderror
</div>
