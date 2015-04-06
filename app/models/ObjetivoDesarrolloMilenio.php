<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ObjetivoDesarrolloMilenio extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoODM";

	public function hijos(){
		return $this->hasMany('ObjetivoDesarrolloMilenio','idPadre')->with('hijos');
	}

	public function padre(){
		return $this->hasOne('ObjetivoDesarrolloMilenio','id','idPadre')->with('padre');
	}
}