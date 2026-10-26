@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false])
<div class="form-group">
    <label for="{{ $name }}">{{ $label }} @if($required)<span class="req">*</span>@endif</label>
    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        @if($type !== 'password') value="{{ old($name, $value) }}" @endif
        @required($required)
        {{ $attributes->class(['is-invalid' => $errors->has($name)]) }}
    >
    @error($name)
        <small class="error">{{ $message }}</small>
    @enderror
</div>
