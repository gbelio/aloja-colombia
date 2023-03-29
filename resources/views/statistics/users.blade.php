@extends('layouts.app')
@section('content')
    <div class="__data">
        <h1 style="text-align: center;">USUARIOS</h1>
        <br>
        <br>
        <div class="__cards" style="display: flex;justify-content: space-evenly;">
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Total de Huespedes</h2>
                <p>{{$huespedesTotal}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Total de Propietarios</h2>
                <p>{{$propietariosTotal}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Propiedades Activas</h2>
                <p>{{$alojamientosTotal}}</p>
            </div>
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Propiedades Inactivas</h2>
                <p>{{$alojamientosInactivos}}</p>
            </div>
            @isset($usuariosTotal)
            <div class="__card" style="font-size: 4rem;border: 1px solid red;border-radius: 10px;padding: 10px 25px;text-align: center;width:230px;">
                <h2>Usuarios registrados</h2>
                <p>{{$usuariosTotal}}</p>
            </div>
            @endisset
        </div>
    </div>
    <br>
    <div class="col-12 searchBar">
        <form action="/busqueda/user" class="offset-1" method="get" style="">
            @csrf
            <input required placeholder='email, nombre, apellido...' type="text" name="clave">
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
        <div class="__tabla" style="max-width: 90vw; margin:auto;">
            <table class="table table-hover table-striped">
                <thead style="text-align: center;">
                    <tr>
                        <th scope="col">Nombre</th>
                        <th scope="col">Apellido</th>
                        <th scope="col">Email</th>
                        <th scope="col">Teléfono</th>
                        <th scope="col">Propiedades</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr style="text-align: center;">
                        <td>{{$user->name}}</td>
                        <td>{{$user->apellido}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->celular}}</td>
                        @if($user->property->count())
                        <td style="text-align: center;"><b><a href="/alojamientos/user/{{$user->id}}">{{$user->property->count()}}</a></b></td>
                        @else
                        <td style="text-align: center;"><b>-</b></td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="__pagination" style="display: flex;justify-content: space-evenly;">
                @if($users != []){{ $users->links() }}@endif
            </div>
            <a href="{{ url()->previous() }}" class="boton-volver" role="button">
                <button class="__boton-trans btn btn-outline-danger" style="margin-left: 50px;">Volver</button>
            </a>
            <br>
            <br>
            <br>
            <br>
        </div>
    </div>
</body>
@endsection