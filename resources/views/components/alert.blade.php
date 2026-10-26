@props(['type' => 'success'])
<div {{ $attributes->class(['alert', "alert-{$type}"]) }} role="alert">{{ $slot }}</div>
