<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use Auth;
use Mail;
use Storage;
use App\User;

use Illuminate\Support\Facades\Redirect;

class UsersController extends Controller
{


    public function tieneAcceso($usuario) {

        if ( !Auth::user()->esAdministrador() ) {
          if ( $usuario->id != \Auth::user()->id  ) {
            abort('403');
          }
        }

    } 

    public function index(Request $request)
    {
    }

    public function create()
    {
    }

    public function store(UsersRequest $request)
    {
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
       $user = User::find($id);

       $this->tieneAcceso($user);

       return View('users.save')
          ->with('user', $user)
          ->with('method', 'PUT');
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        // Imagen
        $nuevoArchivo = false;
        if ( $request->hasFile('foto') && $request->file('foto')->isValid() ) {
          // Subo el documento
          $file = $request->file('foto');
          $nombre = $file->getClientOriginalName();
          $path = $request->file('foto')->storeAs(
            'users/' . $id . '/foto', $nombre
            );
          $nuevoArchivo = true;
          // Elimino el anterior si existe
          if ( $user->foto != null ) {
            $foto = $user->foto;
            if (Storage::exists($foto)) {
              Storage::delete($foto);
              } 
            }
          }

        // Si no hay documento nuevo y eligió borrarlo
        $borrarArchivoSolamente = false;
        if (!$nuevoArchivo && $request->borrar_archivo == 'SI') {
         $borrarArchivoSolamente = true;
         if ( $user->foto != null ) {
          $foto = $user->foto;
          if (Storage::exists($foto)) {
            Storage::delete($foto);
            } 
          }
        }

        if ($nuevoArchivo) {
         $user->foto = $path;
        }
        else if  ($borrarArchivoSolamente) {
         $user->foto = null;
        }

        $user->name = $request->name;
        $user->apellido = $request->apellido;
        $user->celular = $request->celular;
        $user->descripcion = $request->descripcion;

        $user->save();

        return Redirect::to($request->origen)->with('notice', 'Perfil modificado con éxito');
    }

    public function destroy($id)
    {
    }

    public function showChangePasswordForm(){
        return view('users.changePassword');
    }

    public function changePassword(Request $request){
 
        if (!(\Hash::check($request->get('current-password'), Auth::user()->password))) {
            return redirect()->back()->with("error","Su contraseña actual no coincide con la contraseña ingresada. Por favor, intente nuevamente.");
        }
 
        if(strcmp($request->get('current-password'), $request->get('password')) == 0){
            return redirect()->back()->with("error","La contraseña nueva no puede coincidir con su contraseña actual. Por favor, elija una contraseña diferente.");
        }
 
        $validatedData = $request->validate([
            'current-password' => 'required',
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
 
        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->save();
 
        return redirect()->back()->with("success","Contraseña modificada con éxito.");
 
    }
   

}
