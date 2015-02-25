<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Beneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectoBeneficiarios";
	
	public function tipoBeneficiario(){
		
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
		
	}
}

