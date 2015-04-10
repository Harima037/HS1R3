<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ObjetivoPND extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoObjetivosPND";

	public function hijos(){
		return $this->hasMany('ObjetivoPND','idPadre')->with('hijos');
	}

	public function padre(){
		return $this->hasOne('ObjetivoPND','id','idPadre')->with('padre');
	}
}