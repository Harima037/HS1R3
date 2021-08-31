<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Estrategia extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "estrategia";
	protected $guarded = array('claveProgramaPresupuestario', 'claveUnidadResponsable', 'claveProgramaSectorial', 'idOdm', 'idObjetivoPED', 'idEstatus', 'ejercicio', 'mision', 'vision', 'descripcionIndicador', 'numerador', 'denominador', 'interpretacion', 'idTipoIndicador', 'idDimensionIndicador', 'idUnidadMedida', 'metaIndicador', 'lineaBase', 'anioBase', 'idFormula', 'idFrecuenciaIndicador', 'trim1', 'trim2', 'trim3', 'trim4', 'valorNumerador', 'valorDenominador', 'idResponsable', 'fuenteInformacion');

	public function scopeContenidoSuggester($query){
		$query->select('estrategia.id','estrategia.claveProgramaPresupuestario','estrategia.descripcionIndicador','estrategia.claveUnidadResponsable','estrategia.idEstatus',
				DB::raw('concat_ws(" ",programaPresupuestal.clave,programaPresupuestal.descripcion) as programaPresupuestario'),
				DB::raw('concat_ws(" ",estrategia.claveUnidadResponsable,unidadResponsable.descripcion) AS unidadResponsable'),
				'estatus.descripcion AS estatus','estrategia.idUsuarioValidacionSeg','estrategia.idUsuarioRendCuenta'
			)
			->leftjoin('catalogoProgramasPresupuestales AS programaPresupuestal','programaPresupuestal.clave','=','estrategia.claveProgramaPresupuestario')
			->leftjoin('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','estrategia.claveUnidadResponsable')
			->leftjoin('catalogoEstatusProyectos AS estatus','estatus.id','=','estrategia.idEstatus');
	}

	public function programaPresupuestario(){
		return $this->belongsTo('ProgramaPresupuestario','claveProgramaPresupuestario','clave');
	}

	public function formula(){
		return $this->belongsTo('Formula','idFormula','id');
	}

	public function frecuencia(){
		return $this->belongsTo('Frecuencia','idFrecuenciaIndicador','id');
	}

	public function unidadResponsable(){
		return $this->belongsTo('UnidadResponsable','claveUnidadResponsable','clave');
	}

	public function programaSectorial(){
		return $this->belongsTo('ProgramaSectorial','claveProgramaSectorial','clave');
	}

	public function odm(){
		return $this->belongsTo('ObjetivoDesarrolloMilenio','idOdm','id');
	}

	public function ped(){
		return $this->belongsTo('ObjetivoPED','idObjetivoPED','id');
	}

	public function tipoIndicador(){
		return $this->belongsTo('TipoIndicador','idTipoIndicador','id');
	}

	public function dimension(){
		return $this->belongsTo('Dimension','idDimensionIndicador','id');
	}

	public function unidadMedida(){
		return $this->belongsTo('UnidadMedida','idUnidadMedida','id');
	}

	public function responsable(){
		return $this->belongsTo('Directorio','idResponsable','id');
	}

	public function Estatus(){
		return $this->belongsTo('EstatusProyecto','idEstatus','id');
	}

	public function comentario(){
		return $this->hasMany('EstrategiaComentario','idEstrategia');
	}

	public function Usuario(){
		return $this->belongsTo('SentryUser','creadoPor','id')->withTrashed();
	}

	public function registroAvance(){
		return $this->hasMany('RegistroAvanceEstrategia','idEstrategia');
	}

	public function evaluacionTrimestre(){
		return $this->hasMany('EvaluacionEstrategiaTrimestre','idEstrategia');
	}
}

?>