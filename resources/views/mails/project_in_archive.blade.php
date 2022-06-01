@component('mail::message')
Hallo **{{$name}}**,

@component('mail::panel')
    dein Projekt **{{$title}}** wurde archiviert.Du findest es nun im Archiv,
    das du als Menüpunkt unter deinem Profil findest. Änderungen am Projekt sind nun nicht mehr möglich.
@endcomponent

@component('mail::button', ['url' => $redirectUrl])
    Zum archivierten Projekt
@endcomponent

<br>
<small>Falls der Link nicht funktionieren sollte: {{$redirectUrl}}</small>
@endcomponent
