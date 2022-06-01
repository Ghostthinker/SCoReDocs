@component('mail::message')
Hallo **{{$name}}**
<br>
<br>
es gibt eine neue News-Meldung:
<br>
@component('mail::panel')
    {!! $content !!}
@endcomponent

@component('mail::button', ['url' => $redirectUrl])
    Zur News
@endcomponent
<br>
<small>Falls der Link nicht funktionieren sollte: {{$redirectUrl}}</small>
@endcomponent
