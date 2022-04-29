<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Correo extends Model
{
	public function fechaFormateada()
	{
   		return date('d/m/Y H:i', strtotime($this->date));
	}
}
