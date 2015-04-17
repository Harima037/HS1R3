<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Beneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectoBeneficiarios";

	public function scopeConDescripcion($query){
		return $query->join('catalogoTiposBeneficiarios AS tipoBeneficiario','tipoBeneficiario.id','=','proyectoBeneficiarios.idTipoBeneficiario')
					->select('proyectoBeneficiarios.*','tipoBeneficiario.descripcion AS tipoBeneficiario');
	}

	public function tipoBeneficiario(){
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
	}

	public function registroAvance(){
    	return $this->hasMany('RegistroAvanceBeneficiario','idProyectoBeneficiario');
    }

	public function registroAvanceAcumulado(){
    	return $this->hasMany('RegistroAvanceBeneficiario','idProyectoBeneficiario');
    }    
	public function comentarios(){
    	return $this->hasMany('EvaluacionComentario','idElemento')->where('tipoElemento','=',1);
    }
}