<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class IndicadorFASSAMeta extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "indicadorFASSAMeta";

	public function scopeConDetalle($query){
		return $query->select('indicadorFASSAMeta.*','liderPrograma.nombre AS nombreLiderPrograma',
					'liderPrograma.cargo AS cargoLiderPrograma','responsableInformacion.nombre AS nombreResponsableInformacion',
					'responsableInformacion.cargo AS cargoResponsableInformacion')
					->leftjoin('vistaDirectorio AS liderPrograma','liderPrograma.id','=','indicadorFASSAMeta.idLiderPrograma')
					->leftjoin('vistaDirectorio AS responsableInformacion','responsableInformacion.id','=','indicadorFASSAMeta.idResponsableInformacion')
					->orderBy('ejercicio','asc');
	}

	public function indicador(){
		return $this->belongsTo('IndicadorFASSA','idIndicadorFASSA');
	}
}