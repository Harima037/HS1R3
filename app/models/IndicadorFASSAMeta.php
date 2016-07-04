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
					'responsableInformacion.cargo AS cargoResponsableInformacion','estatus.descripcion AS estatus','estatusCierre.descripcion AS estatusCierre')
					->leftjoin('vistaDirectorio AS liderPrograma','liderPrograma.id','=','indicadorFASSAMeta.idLiderPrograma')
					->leftjoin('vistaDirectorio AS responsableInformacion','responsableInformacion.id','=','indicadorFASSAMeta.idResponsableInformacion')
					->leftjoin('catalogoEstatusProyectos AS estatus','estatus.id','=','indicadorFASSAMeta.idEstatus')
					->leftjoin('catalogoEstatusProyectos AS estatusCierre','estatusCierre.id','=','indicadorFASSAMeta.idEstatusCierre')
					->orderBy('ejercicio','asc');
	}

	public function scopeIndicadoresEjercicio($query){
		return $query->select('indicadorFASSAMeta.*','indicadorFASSA.claveNivel','indicadorFASSA.indicador',
					'estatus.descripcion AS estatus','estatusCierre.descripcion AS estatusCierre','unidad.descripcion AS unidadResponsable')
					->join('indicadorFASSA','indicadorFASSA.id','=','indicadorFASSAMeta.idIndicadorFASSA')
					->leftjoin('catalogoEstatusProyectos AS estatus','estatus.id','=','indicadorFASSAMeta.idEstatus')
					->leftjoin('catalogoEstatusProyectos AS estatusCierre','estatusCierre.id','=','indicadorFASSAMeta.idEstatusCierre')
					->leftjoin('catalogoUnidadesResponsables AS unidad','unidad.clave','=','indicadorFASSAMeta.claveUnidadResponsable');

	}

	public function scopeIndicadorMetaDetalle($query){
		return $query->select('indicadorFASSAMeta.*','liderPrograma.nombre AS nombreLiderPrograma',
					'liderPrograma.cargo AS cargoLiderPrograma','responsableInformacion.nombre AS nombreResponsableInformacion',
					'responsableInformacion.cargo AS cargoResponsableInformacion','estatus.descripcion AS estatus',
					'estatusCierre.descripcion AS estatusCierre','indicadorFASSA.claveNivel','indicadorFASSA.indicador',
					'indicadorFASSA.formula','indicadorFASSA.fuenteInformacion','indicadorFASSA.claveTipoFormula','indicadorFASSA.tasa')
					->join('indicadorFASSA','indicadorFASSA.id','=','indicadorFASSAMeta.idIndicadorFASSA')
					->leftjoin('vistaDirectorio AS liderPrograma','liderPrograma.id','=','indicadorFASSAMeta.idLiderPrograma')
					->leftjoin('vistaDirectorio AS responsableInformacion','responsableInformacion.id','=','indicadorFASSAMeta.idResponsableInformacion')
					->leftjoin('catalogoEstatusProyectos AS estatus','estatus.id','=','indicadorFASSAMeta.idEstatus')
					->leftjoin('catalogoEstatusProyectos AS estatusCierre','estatusCierre.id','=','indicadorFASSAMeta.idEstatusCierre');
	}

	public function indicador(){
		return $this->belongsTo('IndicadorFASSA','idIndicadorFASSA');
	}

	public function metasTrimestre(){
		return $this->hasMany('IndicadorFASSAMetaTrimestre','idIndicadorFASSAMeta')->orderBy('trimestre');
	}

	public function registroAvance(){
		return $this->hasMany('RegistroAvanceIndicadorFASSA','idIndicadorFASSAMeta')->conDetalle()->orderBy('mes');
	}
	
	public function comentario(){
		return $this->hasMany('IndicadorFASSAMetaComentarios','idIndicadorFASSAMeta','id');
	}
	
	
}