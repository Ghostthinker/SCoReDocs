@component('mail::message')
Hallo **{{$name}}**
@component('mail::panel')
Wow, es ist einiges passiert heute! <br>
{!! $text !!}
@endcomponent

@component('mail::button', ['url' => $redirectUrl])
    Zum Projekt
@endcomponent
<br>
<small>Falls der Link nicht funktionieren sollte: {{$redirectUrl}}</small>
@endcomponent
