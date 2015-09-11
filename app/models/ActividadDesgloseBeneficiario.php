<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ActividadDesgloseBeneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "actividadDesgloseBeneficiarios";

	public function tipoBeneficiario(){
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
	}

	public function scopeConDescripcion($query){
		return $query->select('actividadDesgloseBeneficiarios.*','tipoBeneficiario.descripcion AS tipoBeneficiario')
				->join('catalogoTiposBeneficiarios AS tipoBeneficiario','tipoBeneficiario.id','=','actividadDesgloseBeneficiarios.idTipoBeneficiario');
	}
}