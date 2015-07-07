<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class BitacoraCargaEPRegion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "bitacoraCargaEPRegion";

	public function datos(){
		return $this->hasMany('CargaDatosEPRegion','idBitacoraCargaEPRegion');
	}
}