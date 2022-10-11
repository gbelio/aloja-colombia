@extends('layouts.app')
@section('content')
<div class="container-fluid alojar">
    <div class="row">
        <div class="aloja-principal-pc col-md-12 text-center" style="background-image: url({{ asset('img/aloja-pc.jpg')}})">
            <div class="aloja-principal-titulo">Ya te encuentras a un paso de ser alojador</div>
            <div class="aloja-principal-leyenda">FÁCIL, RÁPIDO Y SEGURO.</div>
            <div class="aloja-principal-pasos">
                <span>1</span> 
                Registrate
                <br/>
                <span>2</span> 
                Carga los datos de tu alojamiento
                <br/>
                <span>3</span> 
                Empieza a ganar dinero.
            </div>
            {!! link_to('alojamientos', 'Comienza', ['class' => 'btn home-alojar2-boton' ]) !!}
        </div>
        <div class="aloja-principal-celu aloja-principal-celu-texto col-md-6">
            <div class="aloja-principal-titulo">Ya te encuentras a un paso de ser alojador</div>
            <div class="aloja-principal-leyenda">FÁCIL, RÁPIDO Y SEGURO.</div>
            <div class="aloja-principal-pasos">
                <span>1</span> 
                Registrate
                <br/>
                <span>2</span> 
                Carga los datos de tu alojamiento
                <br/>
                <span>3</span> 
                Empieza a ganar dinero.
            </div>
            {!! link_to('alojamientos', 'Comienza', ['class' => 'btn home-alojar2-boton' ]) !!}
        </div>
        <div class="aloja-principal-celu aloja-principal-celu-imagen col-md-6">
            <img class="" src="{{asset('img/aloja-celu.jpg')}}">
        </div>
    </div>
     <div class="row">
        <div class="col-md-1"></div>
        <div class="aloja-personas-leyenda1 col-md-6">Cada vez más Colombianos eligen Alojar con Aloja Colombia</div>
        <div class="col-md-5"></div>
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-4">
                    <div class="aloja-personas-imagen"><img class="aloja-personas-imagen" src="{{asset('img/sonia.jpg')}}"></div>
                    <div class="aloja-personas-nombre">Sonia Caicedo</div>
                    <div class="aloja-personas-texto">
                        “Desde que conocí Aloja Colombia y comencé a publicar mi propiedad solo tengo que dejar las llaves en portería 
                        y ellos se encargan de conseguirme el huésped para poder alquilarlo!
                        <br/>
                        ¡Me encanta!”
                    </div>
                </div>
                <div class="col-md-4">
                    <img class="aloja-personas-imagen" src="{{asset('img/andres.jpg')}}">
                    <div class="aloja-personas-nombre">Andrés F. Jaramillo</div>
                    <div class="aloja-personas-texto">
                        “Aloja Colombia ha demostrado ser una plataforma amigable, cercana, de fácil acceso, 
                        publique mi Alojamiento y ahora tengo ingresos adicionales de una manera fácil, segura y lo mejor me transfieren 
                        directamente en mi cuenta bancaria de una manera segura!
                        <br/>
                        ¡Se las recomiendo!”
                    </div>
                </div>
                <div class="col-md-4">
                    <img class="aloja-personas-imagen" src="{{asset('img/juli.jpg')}}">
                    <div class="aloja-personas-nombre">Juliana Gómez</div>
                    <div class="aloja-personas-texto">
                        “Recomiendo Aloja Colombia por la tranquilidad que me brinda al publicar mi propiedad 
                        y su gran respaldo y amabilidad hacia los propietarios. 
                        ¡Esto es lo que estaba buscando!
                        <br/>
                        Gracias Aloja Colombia.”
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-1"></div>
        <div class="aloja-personas-leyenda2 col-md-10 text-center">Ven y conoce esta nueva experiencia, haz parte de esta gran comunidad</div>
    </div>
</div>
@endsection
