@extends('layouts.app')

@section('content')

 <div class="container login-aloja">





    <div class="row justify-content-center">

        <div class="col-md-8">



        {!! Form::open(array('url' => 'users/' . $user->id, 'method' => $method, 'enctype' => 'multipart/form-data')) !!}

           {!! Form::hidden('borrar_archivo', null, ['id' => 'borrar_archivo']) !!}

           {!! Form::hidden('origen', URL::previous()) !!}



           <div class="card">

             <div class="card-header">Editar perfil</div>

             <div class="card-body">



              <div class="form-row">



               <div class="form-group col-md-12">

                  {!! Form::label('name', __('Name') ) !!}

                  {!! Form::text('name', $user->name, ['class' => 'form-control', 'required' => 'required',  'maxlength' => '255']) !!}

               </div>



               <div class="form-group col-md-12">

                  {!! Form::label('apellido', 'Apellido' ) !!}

                  {!! Form::text('apellido', $user->apellido, ['class' => 'form-control', 'required' => 'required',  'maxlength' => '100']) !!}

               </div>



               <div class="form-group col-md-12">

                  {!! Form::label('celular', 'Celular' ) !!}

                  {!! Form::text('celular', $user->celular, ['class' => 'form-control', 'required' => 'required']) !!}

               </div>



               <div class="form-group col-md-12">

                  {!! Form::label('email', __('E-Mail Address')) !!}

                  {!! Form::email('email', $user->email, ['class' => 'form-control', 'disabled' => 'disabled']) !!}

               </div>



               <div class="form-group col-md-12">

                  {!! Form::label('descripcion', __('Información adicional')) !!}

                  {!! Form::textarea('descripcion', $user->descripcion, ['class' => 'form-control', 'maxlength' => '255', 'rows' => '3']) !!}

               </div>



               <div class="form-group col-md-12">

                  {!! Form::label('foto', 'Foto de perfil' ) !!}<br/>

                  @if ( $user->id != null && $user->foto != null )

                  <div id="bloque_borrar_archivo">

                    Elija una nueva foto para reemplazar la actual o presione <a href="#" onclick="$('#borrar_archivo').val('SI'); $('#bloque_borrar_archivo').css('display','none'); return false;">borrar</a> para eliminarla.

                    <br/>

                  </div>

                  @endif

                  {!! Form::file('foto', null, ['class' => 'form-control']) !!}

                  <br/>

               </div>



               </div>

               <div class="form-row">



                <div class="form-group col-md-12 text-center">

                  @if ( $user->id != null && $user->foto != null )

                    <img class="imagenReal" src="{{$user->sourceFoto()}}"/>

                  @endif

                </div>



                </div>



             </div>



           </div>



         <br/>



           {!! Form::submit( 'Guardar', ['class' => 'btn btn-primary']) !!} 

           {!! link_to(URL::previous(), 'Cancelar', ['class' => 'btn btn-secondary']) !!}

        {!! Form::close() !!}



      </div>



    </div>

 </div>

@endsection