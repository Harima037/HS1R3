<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class BitacoraCargaEP01 extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "bitacoraCargaEP01";

	public function datos(){
		return $this->hasMany('CargaDatosEP01','idBitacoraCargaEP01');
	}
}