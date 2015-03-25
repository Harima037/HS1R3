<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ObjetivoPED extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoObjetivosPED";

	public function hijos(){
		return $this->hasMany('ObjetivoPED','idPadre')->with('hijos');
	}

	public function padre(){
		return $this->hasOne('ObjetivoPED','id','idPadre')->with('padre');
	}
}