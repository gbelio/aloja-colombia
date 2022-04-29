@extends('layouts.app')

@section('content')
<div class="container login-aloja">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Registrate</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <div class="col-md-12">
                                <h2>Bienvenido a Aloja Colombia</h2>
                                <input placeholder="{{ __('Name') }}" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="Apellido" id="apellido" type="text" class="form-control" name="apellido" value="{{ old('apellido') }}" value="" required>
                            </div>

                                @error('apellido')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="{{ __('E-Mail Address') }}" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="Celular" id="celular" type="tel" class="form-control" name="celular" value="{{ old('celular') }}" value="" required>
                            </div>

                                @error('celular')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="{{ __('Password') }}" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                    
                                <small class="form-text text-muted">Mínimo 10 caracteres. Al menos una letra minúscula, una mayúscula y un número.</small>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <input placeholder="{{ __('Confirm Password') }}" id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terminos" id="terminos" required>
                                    <label class="form-check-label" for="terminos">Acepto</label>&nbsp;{!! link_to('/doc/terminos.pdf', 'Términos y Condiciones', ['target' => '_blank', 'class' => 'pie-link2' ]) !!}
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="privacidad" id="privacidad" required>
                                    <label class="form-check-label" for="privacidad">Acepto</label>&nbsp;{!! link_to('/doc/privacidad.pdf', 'Política de Privacidad', ['target' => '_blank', 'class' => 'pie-link2' ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    Crear cuenta
                                </button>
                            </div>
                        </div>
                    </form>


                    <br/>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection
