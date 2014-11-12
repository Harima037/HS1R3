<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FuncionGasto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoFuncionesGasto";

	public function hijos(){
		return $this->hasMany('FuncionGasto','idPadre')->with('hijos');
	}
}