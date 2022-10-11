@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
©{{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')<br/>
{!! link_to('/doc/terminos.pdf', 'Términos y Condiciones', ['target' => '_blank']) !!}<br/>
{!! link_to('/doc/privacidad.pdf', 'Política de Privacidad', ['target' => '_blank']) !!}
@endcomponent
@endslot
@endcomponent
