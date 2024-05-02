@extends('layouts.app')

@section('content')
    <div class="container-fluid inicio">
        <div class="row">
            <div class="home-principal cb-slideshow col-md-12 text-center">
                <li><span>Image 01</span></li>
                <li><span>Image 02</span></li>
                <li><span>Image 03</span></li>
                <li><span>Image 04</span></li>
                <li><span>Image 05</span></li>
                {{-- <img src="{{ asset('img/home-min.jpg') }}" class="home-principal-pc"> --}}
                {{-- <img src="{{ asset('img/home-mobile.jpg') }}" class="home-principal-celu"> --}}
                <div class="home-principal-titulo">Porque siempre tendrás seguridad a donde vayas.</div>
            </div>
        </div>
        <div class="home-opciones-viajar">¿A dónde quieres viajar?</div>
        <div class="row home-opciones-row"
            style="border: 5px solid white; border-bottom: 0px!important; max-width: 1380px; margin: auto;">

            <div class="home-opciones col-md-6 text-center" style="padding: 0px;">
                <div class="home-opciones-contenedor"
                    style="background-image: url({{ asset('img/playa-min.jpg') }}); border-radius: 20px; border: 3px solid white; border-bottom: 0px; display: table; width: 100%;">
                    <div style="display: table-cell; vertical-align: middle;">
                        <div class="home-opciones-titulo">Cerca de la playa</div>
                        <div class="home-opciones-leyenda">Alojate y disfruta de la<br />brisa y el mar.</div>
                        {!! link_to('alojamientos/busqueda?opcion=pl', 'Quiero verlos', ['class' => 'btn home-opciones-boton']) !!}
                    </div>
                </div>
            </div>

            <div class="home-opciones col-md-6 text-center" style="padding: 0px;">
                <div class="home-opciones-contenedor"
                    style="background-image: url({{ asset('img/naturaleza-min.jpg') }}); border-radius: 20px; border: 3px solid white; border-bottom: 0px; display: table; width: 100%;">
                    <div style="display: table-cell; vertical-align: middle;">
                        <div class="home-opciones-titulo">Conectate con<br />la naturaleza</div>
                        <div class="home-opciones-leyenda">Alojate y vive todo lo que la<br />naturaleza tiene para ti.</div>
                        {!! link_to('alojamientos/busqueda?opcion=na', 'Quiero verlos', ['class' => 'btn home-opciones-boton']) !!}
                    </div>
                </div>
            </div>

        </div>

        <div class="row home-opciones-row"
            style="border: 5px solid white; border-bottom: 0px!important;  max-width: 1380px; margin: auto;">

            <div class="home-opciones col-md-6 text-center" style="padding: 0px;">
                <div class="home-opciones-contenedor"
                    style="background-image: url({{ asset('img/ciudad-min.jpg') }}); border-radius: 20px; border: 3px solid white; border-bottom: 0px;  display: table; width: 100%;">
                    <div style="display: table-cell; vertical-align: middle;">
                        <div class="home-opciones-titulo">Ciudades con encanto</div>
                        <div class="home-opciones-leyenda">Alojate dentro de las<br />ciudades más atractivas.</div>
                        {!! link_to('alojamientos/busqueda?opcion=ci', 'Quiero verlos', ['class' => 'btn home-opciones-boton']) !!}
                    </div>
                </div>
            </div>

            <div class="home-opciones col-md-6 text-center" style="padding: 0px;">
                <div class="home-opciones-contenedor"
                    style="background-image: url({{ asset('img/campo-min.jpg') }}); border-radius: 20px; border: 3px solid white; border-bottom: 0px;  display: table; width: 100%;">
                    <div style="display: table-cell; vertical-align: middle;">
                        <div class="home-opciones-titulo">Lugares campestres</div>
                        <div class="home-opciones-leyenda">Alojate en lugares tranquilos,<br />desconectate de la ciudad</div>
                        {!! link_to('alojamientos/busqueda?opcion=ca', 'Quiero verlos', ['class' => 'btn home-opciones-boton']) !!}
                    </div>
                </div>
            </div>

        </div>

        <div class="row home-elegidos-row" style="border:5px solid white; border-bottom: 5px solid white;">
            <div class="home-elegidos-header col-md-12 text-center">
                <div class="home-elegidos-header-imagen"><img src="{{ asset('img/isologo.svg') }}" /></div>
                <div class="home-elegidos-header-titulo">Los lugares más elegidos<br />por nuestra comunidad</div>
                <div class="home-elegidos-header-leyenda">Disfruta de los alojamientos con mas visitas por la experiencia
                    vivida y relación calidad /precio.</div>
            </div>

            <?php
            $alojamientos = App\Alojamiento::inRandomOrder()
                ->where('estado', 'A')
                ->where('destacada', 1)
                ->limit(3)
                ->get();
            ?>

            @foreach ($alojamientos as $alojamiento)
                <?php
                $alojamientoFoto = App\AlojamientoFoto::where('alojamiento_id', $alojamiento->id)
                    ->where('num_foto', 1)
                    ->first();
                ?>

                <div class="home-elegidos col-md-4" style="background-image: url('{{ $alojamientoFoto->srcImagen(660) }}')">
                    {!! link_to('alojamientos/' . $alojamiento->id, $alojamiento->titulo, [
                        'style' => '',
                        'class' => ' btn home-elegidos-boton',
                    ]) !!}
                    <div class="home-elegidos-leyenda">{{ $alojamiento->leyendaHuespedesCuartos() }} |
                        {{ $alojamiento->ciudad }}</div>
                </div>
            @endforeach

        </div>

        <div class="home-porque row" style="max-width: 1280px; margin: auto;">
            <div class="col-md-12 text-center">
                <div class="home-porque-titulo">¿Por qué todos elijen Aloja Colombia?</div>
            </div>

            <div class="home-porque-columna col-md-3 text-center">
                <div class="home-porque-imagen"><img src="{{ asset('img/Iconos-19.svg') }}" /></div>
                <div class="home-porque-subtitulo">Nosotros</div>
                <div class="home-porque-parrafo">
                    ALOJA COLOMBIA es una plataforma 100% Colombiana, que conecta “ALOJADORES” con “ALOJADOS” quienes
                    ofrecen y buscan alojamientos temporales y actividades turísticas.
                </div>
            </div>

            <div class="home-porque-columna col-md-3 text-center">
                <div class="home-porque-imagen"><img src="{{ asset('img/Iconos-21.svg') }}" /></div>
                <div class="home-porque-subtitulo">Confiable</div>
                <div class="home-porque-parrafo">
                    Garantizamos que tu dinero siempre esté resguardado hasta el día que llegues a alojarte y encuentres el
                    lugar que elegiste, como lo soñaste.
                </div>
            </div>

            <div class="home-porque-columna col-md-3 text-center">
                <div class="home-porque-imagen"><img src="{{ asset('img/Iconos-22.svg') }}" /></div>
                <div class="home-porque-subtitulo">Nuestra</div>
                <div class="home-porque-parrafo">
                    Fomentamos y apoyamos el crecimiento de nuestras regiones. Impulsamos y damos a conocer la gran riqueza
                    de nuestro país.
                </div>
            </div>
            <div class="home-porque-columna col-md-3 text-center">
                <div class="home-porque-imagen"><img src="{{ asset('img/Iconos-20.svg') }}" /></div>
                <div class="home-porque-subtitulo">Práctica</div>
                <div class="home-porque-parrafo">
                    Nuestra plataforma es amigable, sencilla, con información actualizada, clara y detallada. Esto te
                    ayudará a encontrar exactamente lo que buscas.
                </div>
            </div>
        </div>

        <div class="home-alojar row">

            <div class="col-sm-1"></div>
            <div class="home-alojar-texto col-sm-5">
                <div class="home-alojar-contenedor">
                    <div class="home-alojar-titulo">Quieres Alojar</div>
                    <div class="home-alojar-leyenda solo-pc">Muestra tu alojamiento y alcanza miles<br />de visitas con
                        nuestra plataforma.</div>
                    <div class="home-alojar-leyenda solo-celu">Muestra tu alojamiento y alcanza milesde visitas con nuestra
                        plataforma.</div>
                    <div class="home-alojar-subtitulo">ALOJA DE FORMA FÁCIL, RÁPIDA Y SEGURA.</div>
                    {!! link_to('alojar', 'Quiero Alojar', ['class' => 'btn home-alojar-boton']) !!}
                </div>
            </div>

            <div class="home-alojar-imagen col-sm-5" style="background-image: url({{ asset('img/alojar.jpg') }})"></div>

        </div>

        <div class="row">

            <div class="col-md-2 text-center"></div>
            <div class="home-alojar-final col-md-8 text-center">
                <div class="home-alojar-final-imagen"><img src="{{ asset('img/bandera.gif') }}" /></div>
                <div class="home-alojar-final-leyenda">Te acompañamos a descubrir nuevos destinos y la riqueza cultural que
                    Colombia tiene para ti, acercándote al alojamiento que deseas, con sugerencias para enriquecer tu
                    experiencia.</div>
                <div class="home-alojar-final-titulo">Busca - Encuentra - Vive</div>
            </div>

        </div>

    </div>
@endsection
