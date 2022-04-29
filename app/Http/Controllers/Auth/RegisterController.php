<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
#use Request;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    # register agregado por Juan Pablo para que luego de registrarse vaya al lugar en donde estaba haciendo la reserva
    # https://laraveldaily.com/auth-after-registration-redirect-to-previous-intended-page/

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect()->intended($this->redirectPath());
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:100'],
            'celular' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:10',             // must be at least 10 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                //'regex:/[@$!%*#?&]/', // must contain a special character
            ],
        ]);
    }

    // Antigua validación de password 'password' => ['required', 'string', 'min:8', 'confirmed'],
    // Código repetido en user controller


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {

        $user = User::create([
            'name' => $data['name'],
            'apellido' => $data['apellido'],
            'celular' => $data['celular'],
            'ip_registro' => request()->ip(),
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);        

// Mail al inquilino
        $asunto = 'Bienvenido a Aloja Colombia ' . $user->nombreCompleto() . '!';
        $titulo = 'Bienvenido a Aloja Colombia';
        $cuerpo = 'Hola ' . $user->nombreCompleto() . ',

¡Te damos la bienvenida a Aloja Colombia!

Porque siempre tendrás seguridad a donde vayas, descubre alojamientos y experiencias únicas en toda Colombia.

Aloja Colombia es una comunidad en la que todos pueden pertenecer, por eso debes tratar a todos sus miembros con respeto e igualdad, sin importar raza, religión, nacionalidad, etnia, color de piel, orientación sexual, identidad de género, edad o discapacidad.

Gracias por ser parte de nuestra comunidad!

Saludos,

Equipo Aloja Colombia,';

        $message = Mail::to($user->email);
        $message->send(new \App\Mail\MailGenerico( $asunto, $titulo, $cuerpo));

        return $user;
    }
}
