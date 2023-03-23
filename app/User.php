<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Storage;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'apellido', 'celular', 'ip_registro',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function alojamientos(){
        $this->hasMany('App\Alojamiento');
    }
    
    public function property(){
       return $this->hasMany('App\Alojamiento', 'propietario_id');
    }

    public function esAdministrador(){
        return $this->admin ? true : false;
    }

    public function nombreCompleto(){
        return $this->name . ' ' . $this->apellido;
    }

    public function name(){
        return $this->name;
    }

    public function fechaRegistroFormateada(){
        $fechaDate = \Carbon\Carbon::parse($this->created_at); 
        $fechaFormateada = ucfirst($fechaDate->monthName) . ' de ' . $fechaDate->format('Y');
        return $fechaFormateada;
    }

    public function sourceFoto() {
      $archivo = $this->foto;
      if (Storage::exists($archivo)) {
        $full_path = storage_path() . '/app/'. $archivo;
        $base64 = base64_encode(Storage::get($archivo));
        $image_data = 'data:'.mime_content_type($full_path) . ';base64,' . $base64;
        return $image_data;
      }     
      else {
        return "";
      }   
    }
}
