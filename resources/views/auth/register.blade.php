@extends('layouts.app')

@section('content')
<notification></notification>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Registrieren</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group row">
                            <label for="surname" class="col-md-4 col-form-label text-md-right">Nachname</label>

                            <div class="col-md-6">
                                <input id="surname" type="text" class="form-control @error('surname') is-invalid @enderror" name="surname" value="{{ old('surname') }}" required autocomplete="surname" autofocus>

                                @error('surname')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="firstname" class="col-md-4 col-form-label text-md-right">Vorname</label>

                            <div class="col-md-6">
                                <input id="firstname" type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname" autofocus>

                                @error('firstname')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">E-Mail Adresse</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="matriculation_number" class="col-md-4 col-form-label text-md-right">Matrikelnummer</label>

                            <div class="col-md-6">
                                <input id="matriculation_number" type="number" class="form-control @error('matriculation_number') is-invalid @enderror" name="matriculation_number" value="{{ old('matriculation_number') }}" required>

                                @error('matriculation_number')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="university" class="col-md-4 col-form-label text-md-right">Hochschule</label>

                            <div class="col-md-6">
                                <input id="university" type="text" class="form-control @error('university') is-invalid @enderror" name="university" value="{{ old('university') }}" required>

                                @error('university')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Passwort</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Passwort bestätigen</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">

                            <div class="row">
                                <div class="col-md-6 col-sm-12 offset-md-4" style="display: flex">
                                    <input id="privacy" required name="privacy" type="checkbox" style="justify-content: flex-start; margin: 5px">
                                    @error('privacy')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <privacy style="justify-content: flex-end"></privacy>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12 offset-md-4" style="display: flex">
                                    <input id="terms" required name="terms" type="checkbox" style="justify-content: flex-start; margin: 5px">
                                    @error('terms')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <terms style="justify-content: flex-end"></terms>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12 offset-md-4" style="display: flex">
                                    <input id="student" required name="student" type="checkbox" style="justify-content: flex-start; margin: 5px">
                                    @error('student')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span>Hiermit bestätige ich, dass ich an einer Hochschule oder Universität in Deutschland studiere</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12  offset-md-4">
                                    <span>
                                        Diese Lehrveranstaltung findet im Rahmen eines Forschungsprojekts statt. Aus diesem Grund ist es notwendig, dass Sie die
                                        <a href="/files/FnZ_Einwilligungserklaerung.pdf"> verlinkte Einverständniserklärung </a> zur Datenverarbeitung unterschreiben und hochladen.
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-12 offset-md-4">
                                    <label for="upload-privacy-agreement" class="col-md-4 col-form-label text-md-right"></label>
                                    <input id="upload-privacy-agreement" required name="upload_privacy_agreement" type="file" accept="image/jpeg,application/pdf">
                                    @error('upload_privacy_agreement')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary" style="background-color:  var(--v-anchor-base); border-color:  var(--v-anchor-base)">
                                    Registrieren
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<scorefooter :absolute="false"></scorefooter>
@endsection
