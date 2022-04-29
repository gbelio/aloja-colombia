@extends('layouts.app')
 
@section('content')
<div class="container login-aloja">

    <div class="row justify-content-center">

        <div class="col-md-8">

           <div class="card">
             <div class="card-header">Cambiar contraseña</div>
             <div class="card-body">

              <div class="form-row">

               <div class="form-group col-md-12">                


                
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                    <form class="form-horizontal" method="POST" action="{{ route('changePassword') }}">
                        {{ csrf_field() }}
 
                        <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                            <label for="new-password" class="col-md-4 control-label">Contraseña actual</label>
 
                            <div class="col-md-12">
                                <input id="current-password" type="password" class="form-control" name="current-password" required>
 
                                @if ($errors->has('current-password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('current-password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
 
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Contraseña nueva</label>
 
                            <div class="col-md-12">
                                <input id="password" type="password" class="form-control" name="password" required>

                                <small class="form-text text-muted">Mínimo 10 caracteres. Al menos una letra minúscula, una mayúscula y un número.</small>
 
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
 
                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirmar contraseña nueva</label>
 
                            <div class="col-md-12">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>
 
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Cambiar contraseña
                                </button>
                            </div>
                        </div>
                    </form>
               </div>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection
 