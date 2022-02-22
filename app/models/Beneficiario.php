<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Beneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectoBeneficiarios";

	public function scopeConDescripcion($query){
		return $query->leftJoin('catalogoTiposBeneficiarios AS tipoBeneficiario','tipoBeneficiario.id','=','proyectoBeneficiarios.idTipoBeneficiario')
					->leftJoin('catalogoTiposCaptura AS tipoCaptura','tipoCaptura.id','=','proyectoBeneficiarios.idTipoCaptura')
					->select('proyectoBeneficiarios.*','tipoBeneficiario.descripcion AS tipoBeneficiario','tipoCaptura.descripcion AS tipoCaptura','tipoBeneficiario.grupo AS grupo');
	}

	public function tipoBeneficiario(){
		return $this->belongsTo('TipoBeneficiario','idTipoBeneficiario');
	}

	public function tipoCaptura(){
		return $this->belongsTo('TipoCaptura','idTipoCaptura');
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