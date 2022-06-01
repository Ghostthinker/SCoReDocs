@component('mail::message')

**{{$student_name}}** hat sein Assessment-Dokument zur Prüfung eingereicht.

@component('mail::panel')
Name: **{{$student_name}}**
<br>
Matrikelnummer: **{{$matriculation_number}}**
@endcomponent

@component('mail::button', ['url' => $linkToAssessment])
Zum Assessment-Dokument
@endcomponent
<br>
<small>Falls der Link nicht funktionieren sollte: {{$linkToAssessment}}</small>

@endcomponent
