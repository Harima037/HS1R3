<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DesgloseBeneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "desgloseBeneficiarios";

	public function tipoBeneficiario(){
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
	}
}