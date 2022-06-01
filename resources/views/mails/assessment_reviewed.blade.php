@component('mail::message')
Hallo **{{$name}}**
@component('mail::panel')
    Ihr Bewertungsbogen wurde ausgefüllt.
@endcomponent

@component('mail::button', ['url' => $redirectUrl])
    Zum Bewertungsbogen
@endcomponent
<br>
<small>Falls der Link nicht funktionieren sollte: {{$redirectUrl}}</small>
@endcomponent
