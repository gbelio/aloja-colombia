@extends('layouts.app')
@section('content')
    <div class="__data">
        <h1 style="text-align: center;">Propiedades de {{$user->name}}</h1>
        <br>
        <br>
        <div class="__cards" style="display: flex;justify-content: space-evenly;">
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Total de Propiedades</h2>
                <p>{{$totalAlojamientos}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Propiedades Activas</h2>
                <p>{{$totalActivos}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Propiedades Inactivas</h2>
                <p>{{$totalInactivos}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Propiedades Incompletas</h2>
                <p>{{$totalIncompletos}}</p>
            </div>
        </div>
    </div>
    <br>
    <div class="col-12 searchBar">
        <form action="/busqueda/property" class="offset-1" method="get" style="">
            @csrf
            <input required placeholder='Código de propiedad...' type="text" name="clave" autofocus>
            <button type="submit" value="" class="btn btn-success" name="" id="">
                BUSCAR
            </button>
        </form>
    </div>
    @isset($error)
    <span class="offset-1" style="color: red">{{$error}}</span>
    <br>
    <i class="offset-1">{{$response}}</i>
    <br>
    @endisset
    <br>
    <br>
    <h5 class="offset-1">{{$user->email}}</h5>
    <div class="__tabla" style="max-width: 90vw; margin:auto;">
        <br>
        <table class="table table-hover table-striped">
            <thead style="text-align: center;">
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Título</th>
                    <th scope="col">Alquiler</th>
                    <th scope="col">Alojamiento</th>
                    <th scope="col">Ciudad</th>
                    <th scope="col">Huespedes</th>
                    <th scope="col">Titular</th>
                    <th scope="col">Núm. Documento</th>
                    <th scope="col">Banco-Tipo</th>
                    <th scope="col">Núm. Cuenta</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Carga</th>
                    <th scope="col">Notificación</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($alojamientos as $alojamiento)
                <tr style="text-align: center;">
                    <td><a href="/alojamientos/{{$alojamiento->id}}/edit?paso=11">{{$alojamiento->codigo_alojamiento}}</a></td>
                    <td>{{$alojamiento->titulo}}</td>
                    <td>{{$alojamiento->tipo_alquiler}}</td>
                    <td>{{$alojamiento->tipo_alojamiento}}</td>
                    <td>{{$alojamiento->ciudad}}</td>
                    <td>{{$alojamiento->huespedes_min}}-{{$alojamiento->huespedes}}</td>
                    <td>{{$alojamiento->cuenta_nombre}}</td>
                    <td>{{$alojamiento->cuenta_doc}}</td>
                    <td>{{$alojamiento->cuenta_banco}}<br>{{$alojamiento->cuenta_tipo}}</td>
                    <td>{{$alojamiento->cuenta_nro}}</td>
                    @if($alojamiento->estado == "Inactivo")<td style="color:red;">{{$alojamiento->estado}}@else<td style="color:green;">{{$alojamiento->estado}}@endif</td>
                    @if($alojamiento->carga == "Incompleta")<td style="color:red;">{{$alojamiento->carga}}@else<td style="color:green;">{{$alojamiento->carga}}@endif</td>
                    <td>{{$alojamiento->notification}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ url()->previous() }}" class="boton-volver" role="button">
            <button class="__boton-trans btn btn-outline-danger" style="margin-left: 50px;">Volver</button>
        </a>
        <br>
        <br>
        <br>
        <br>
    </div>
</body>
@endsection
