<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FuenteFinanciamiento extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoFuenteFinanciamiento";

	public function fondos(){
		return $this->hasMany('FuenteFinanciamiento','idPadre')->where('nivel','=',5);
	}
}