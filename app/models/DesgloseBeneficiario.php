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

	public function scopeConDescripcion($query){
		return $query->select('desgloseBeneficiarios.*','tipoBeneficiario.descripcion AS tipoBeneficiario')
				->join('catalogoTiposBeneficiarios AS tipoBeneficiario','tipoBeneficiario.id','=','desgloseBeneficiarios.idTipoBeneficiario');
	}
}