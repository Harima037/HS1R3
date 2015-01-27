<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Entregable extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoEntregables";

	public function acciones(){
		return $this->hasMany('EntregableAccion','idEntregable');
	}

	public function tipos(){
		return $this->hasMany('EntregableTipo','idEntregable');
	}
}