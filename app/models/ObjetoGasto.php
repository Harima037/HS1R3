<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ObjetoGasto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoObjetosGasto";

	public function hijos(){
		return $this->hasMany('ObjetoGasto','idPadre')->with('hijos');
	}
}