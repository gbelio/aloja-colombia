@extends('layouts.app')

@section('content')
<div class="container-fluid inicio">

    <div class="row">
        <div class="home-principal col-md-12 text-center">
            <img class="home-principal-pc" src="{{asset('img/home-min.jpg')}}">
            <img class="home-principal-celu" src="{{asset('img/home-mobile.jpg')}}">
            <div class="home-principal-titulo">Porque siempre tendrás<br/>seguridad a donde vayas.</div>
        </div>
    </div>

    <div class="row home-opciones-row">

        <div class="home-opciones col-md-6 text-center" style="background-image: url({{ asset('img/playa-min.jpg')}})">
            <div class="home-opciones-contenedor">
                <div class="home-opciones-titulo">Cerca de la playa</div>
                <div class="home-opciones-leyenda">Alojate y disfruta de la<br/>brisa y el mar.</div>
                {!! link_to('alojamientos/busqueda?opcion=pl', 'Quiero verlos', ['class' => 'btn home-opciones-boton' ]) !!}
            </div>
        </div>

        <div class="home-opciones col-md-6 text-center" style="background-image: url({{ asset('img/naturaleza-min.jpg')}})">
            <div class="home-opciones-contenedor2">
                <div class="home-opciones-titulo">Conectate con<br/>la naturaleza</div>
                <div class="home-opciones-leyenda">Alojate y vive todo lo que la<br/>naturaleza tiene para ti.</div>
                {!! link_to('alojamientos/busqueda?opcion=na', 'Quiero verlos', ['class' => 'btn home-opciones-boton' ]) !!}
            </div>
        </div>

    </div>

    <div class="row home-opciones-row">

        <div class="home-opciones col-md-6 text-center" style="background-image: url({{ asset('img/ciudad-min.jpg')}})">
            <div class="home-opciones-contenedor">
                <div class="home-opciones-titulo">Ciudades con encanto</div>
                <div class="home-opciones-leyenda">Alojate dentro de las<br/>ciudades más atractivas.</div>
                {!! link_to('alojamientos/busqueda?opcion=ci', 'Quiero verlos', ['class' => 'btn home-opciones-boton' ]) !!}
            </div>
        </div>

        <div class="home-opciones col-md-6 text-center" style="background-image: url({{ asset('img/campo-min.jpg')}})">
            <div class="home-opciones-contenedor">
                <div class="home-opciones-titulo">Lugares campestres</div>
                <div class="home-opciones-leyenda">Alojate en lugares tranquilos,<br/>desconectate de la ciudad</div>
                {!! link_to('alojamientos/busqueda?opcion=ca', 'Quiero verlos', ['class' => 'btn home-opciones-boton' ]) !!}
            </div>
        </div>

    </div>

    <div class="row home-elegidos-row">
        <div class="home-elegidos-header col-md-12 text-center">
            <div class="home-elegidos-header-imagen"><img src="{{ asset('img/isologo.svg')}}"/></div>
            <div class="home-elegidos-header-titulo">Los lugares más elegidos<br/>por nuestra comunidad</div>
            <div class="home-elegidos-header-leyenda">Disfruta de los alojamientos con mas visitas por la experiencia vivida y relación calidad /precio.</div>
        </div>

        <?php
        $alojamientos = App\Alojamiento::inRandomOrder()->where('estado','A')->where('destacada',1)->limit(3)->get(); 
        ?>

        @foreach ($alojamientos as $alojamiento)

          <?php
            $alojamientoFoto = App\AlojamientoFoto::where('alojamiento_id', $alojamiento->id)->where('num_foto', 1)->first();
          ?>

          <div class="home-elegidos col-md-4" style="background-image: url('{{ $alojamientoFoto->srcImagen(660) }}')">
            {!! link_to('alojamientos/'.$alojamiento->id, $alojamiento->titulo, ['style' => '', 'class' => ' btn home-elegidos-boton']) !!}
            <div class="home-elegidos-leyenda">{{$alojamiento->leyendaHuespedesCuartos()}} | {{$alojamiento->ciudad}}</div>
          </div>

        @endforeach  

    </div>    

    <div class="home-porque row">
        <div class="col-md-12 text-center">
            <div class="home-porque-titulo">Porque todos elijen<br>Aloja Colombia</div>
        </div>
        <div class="col-md-3"></div>
        <div class="home-porque-columna col-md-2 text-center">
            <div class="home-porque-imagen"><img src="{{ asset('img/check-blanco.svg')}}"/></div>
            <div class="home-porque-subtitulo">Confiable</div>
            <div class="home-porque-parrafo">
Garantizamos que tu dinero siempre esté resguardado hasta el día que llegues a alojarte y encuentres el lugar que elegiste, como lo soñaste.
            </div>
        </div>
        <div class="home-porque-columna col-md-2 text-center">
            <div class="home-porque-imagen"><img src="{{ asset('img/carita-blanco.svg')}}"/></div>
            <div class="home-porque-subtitulo">Nuestra</div>
            <div class="home-porque-parrafo">
Fomentamos y apoyamos el crecimiento de nuestras regiones. Impulsamos y damos a conocer la gran riqueza de nuestro país.
            </div>
        </div>
        <div class="home-porque-columna col-md-2 text-center">
            <div class="home-porque-imagen"><img src="{{ asset('img/cruz-blanco.svg')}}"/></div>
            <div class="home-porque-subtitulo">Positiva</div>
            <div class="home-porque-parrafo">
Nuestra plataforma es amigable, sencilla, con información actualizada, clara y detallada. Esto te ayudará a encontrar exactamente lo que buscas.
            </div>
        </div>
    </div>   

    <div class="home-alojar row">

        <div class="col-sm-1"></div>
        <div class="home-alojar-texto col-sm-5">
            <div class="home-alojar-contenedor">
                <div class="home-alojar-titulo">Quieres Alojar</div>
                <div class="home-alojar-leyenda solo-pc">Muestra tu alojamiento y alcanza miles<br/>de visitas con nuestra plataforma.</div>
                <div class="home-alojar-leyenda solo-celu">Muestra tu alojamiento y alcanza milesde visitas con nuestra plataforma.</div>
                <div class="home-alojar-subtitulo">ALOJA DE FORMA FÁCIL, RÁPIDA Y SEGURA.</div>
                {!! link_to('alojar', 'Quiero Alojar', ['class' => 'btn home-alojar-boton' ]) !!}
            </div>
        </div>

        <div class="home-alojar-imagen col-sm-5" style="background-image: url({{ asset('img/alojar.jpg')}})"></div>

    </div>

    <div class="row">

        <div class="col-md-2 text-center"></div>
        <div class="home-alojar-final col-md-8 text-center">
            <div class="home-alojar-final-imagen"><img src="{{asset('img/bandera.gif')}}"/></div>
            <div class="home-alojar-final-leyenda">Te acompañamos a descubrir nuevos destinos y la riqueza cultural que Colombia tiene para ti, acercándote al alojamiento que deseas, con sugerencias para enriquecer tu experiencia.</div>
            <div class="home-alojar-final-titulo">Busca - Encuentra - Vive</div>
        </div>

    </div>

</div>
@endsection
