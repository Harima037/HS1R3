<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Estrategia extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "estrategia";
	protected $guarded = array('idEstrategiaNacional', 'claveUnidadResponsable', 'claveProgramaSectorial', 'idOds', 'idObjetivoPED', 'idEstatus', 'ejercicio', 'mision', 'vision', 'descripcionIndicador', 'numerador', 'denominador', 'interpretacion', 'idTipoIndicador', 'idDimensionIndicador', 'idUnidadMedida', 'metaIndicador', 'lineaBase', 'anioBase', 'idFormula', 'idFrecuenciaIndicador', 'trim1', 'trim2', 'trim3', 'trim4', 'valorNumerador', 'valorDenominador', 'idResponsable', 'fuenteInformacion','idComportamientoAccion','idTipoValorMeta');

	public function scopeContenidoSuggester($query){
		$query->select('estrategia.id','estrategia.idEstrategiaNacional','estrategia.descripcionIndicador','estrategia.claveUnidadResponsable','estrategia.idEstatus',
				DB::raw('estrategiaNacional.descripcion as estrategiaNacional'),
				DB::raw('concat_ws(" ",estrategia.claveUnidadResponsable,unidadResponsable.descripcion) AS unidadResponsable'),
				'estatus.descripcion AS estatus','estrategia.idUsuarioValidacionSeg','estrategia.idUsuarioRendCuenta'
			)
			->leftjoin('catalogoEstrategiasNacionales AS estrategiaNacional','estrategiaNacional.id','=','estrategia.idEstrategiaNacional')
			->leftjoin('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','estrategia.claveUnidadResponsable')
			->leftjoin('catalogoEstatusProyectos AS estatus','estatus.id','=','estrategia.idEstatus');
	}

	public function scopeContenidoReporte($query){
		$query->select('estrategia.*', DB::raw('estrategiaNacional.descripcion as estrategiaNacional'), 
					DB::raw('concat_ws(" ",estrategia.claveUnidadResponsable,unidadResponsable.descripcion) AS unidadResponsable'),
					'programaSectorial.descripcion AS programaSectorial', 'ODS.descripcion AS ods','objetivoPED.descripcion as objetivoPED',
					'formula.descripcion AS formula','dimension.descripcion AS dimension','frecuencia.descripcion AS frecuencia','tipoIndicador.descripcion AS tipoIndicador',
    				'unidadMedida.descripcion AS unidadMedida','comportamientoAccion.descripcion as comportamientoAccion','tipoValorMeta.descripcion AS tipoValorMeta',
					'responsable.nombre AS nombreResponsable', 	'responsable.cargo AS cargoResponsable',
					'liderPrograma.nombre AS liderPrograma',	'liderPrograma.cargo AS liderProgramaCargo'
				)
				->leftJoin('catalogoEstrategiasNacionales AS estrategiaNacional','estrategiaNacional.id','=','estrategia.idEstrategiaNacional')
				->leftjoin('catalogoUnidadesResponsables AS unidadResponsable','unidadResponsable.clave','=','estrategia.claveUnidadResponsable')
				->leftjoin('catalogoProgramasSectoriales AS programaSectorial','programaSectorial.clave','=','estrategia.claveProgramaSectorial')
				->leftjoin('catalogoObjetivosDesarrolloSostenible as ODS','ODS.id','=','estrategia.idOds')
				->leftjoin('catalogoObjetivosPED AS objetivoPED','objetivoPED.id','=','estrategia.idObjetivoPED')
				->leftjoin('catalogoFormulas AS formula','formula.id','=','estrategia.idFormula')
				->leftjoin('catalogoDimensionesIndicador AS dimension','dimension.id','=','estrategia.idDimensionIndicador')
				->leftjoin('catalogoFrecuenciasIndicador AS frecuencia','frecuencia.id','=','estrategia.idFrecuenciaIndicador')
				->leftjoin('catalogoTiposIndicadores AS tipoIndicador','tipoIndicador.id','=','estrategia.idTipoIndicador')
				->leftjoin('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','estrategia.idUnidadMedida')
				->leftjoin('catalogoTiposValorMeta AS tipoValorMeta','tipoValorMeta.id','=','idTipoValorMeta')
				->leftjoin('catalogoComportamientosAccion AS comportamientoAccion','comportamientoAccion.id','=','idComportamientoAccion')

				->leftjoin('vistaDirectorio As responsable','responsable.id','=','estrategia.idResponsable')
				->leftjoin('vistaDirectorio AS liderPrograma','liderPrograma.id','=','estrategia.idLiderPrograma')
				;
	}

	public function metasAnios(){
		return $this->hasMany('EstrategiaMetaAnio','idEstrategia')->orderBy('anio');
	}

	public function comportamientoAccion(){
		return $this->belongsTo('ComportamientoAccion','idComportamientoAccion');
	}
	
	public function tipoValorMeta(){
		return $this->belongsTo('TipoValorMeta','idTipoValorMeta');
	}

	public function estrategiaNacional(){
		return $this->belongsTo('EstrategiaNacional','idEstrategiaNacional','id');
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

	public function ods(){
		return $this->belongsTo('ObjetivoDesarrolloSostenible','idOds','id');
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