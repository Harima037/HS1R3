<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Componente extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectoComponentes";
	
	public function scopeContenidoCompleto($query){
		return $query->with('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable',
							'entregableTipo','entregableAccion','desgloseCompleto');
	}

	public function scopeConDescripcion($query){
		return $query->leftjoin('catalogoFormulas AS formula','formula.id','=','proyectoComponentes.idFormula')
					->leftjoin('catalogoDimensionesIndicador AS dimension','dimension.id','=','proyectoComponentes.idDimensionIndicador')
					->leftjoin('catalogoFrecuenciasIndicador AS frecuencia','frecuencia.id','=','proyectoComponentes.idFrecuenciaIndicador')
					->leftjoin('catalogoTiposIndicadores AS tipoIndicador','tipoIndicador.id','=','proyectoComponentes.idTipoIndicador')
					->leftjoin('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','proyectoComponentes.idUnidadMedida')
					->leftjoin('catalogoEntregables AS entregable','entregable.id','=','proyectoComponentes.idEntregable')
    				->leftjoin('catalogoEntregablesTipos AS entregableTipo','entregableTipo.id','=','proyectoComponentes.idEntregableTipo')
    				->leftjoin('catalogoEntregablesAcciones AS entregableAccion','entregableAccion.id','=','proyectoComponentes.idEntregableAccion')
    				->select('proyectoComponentes.*','formula.descripcion AS formula','dimension.descripcion AS dimension',
    					'frecuencia.descripcion AS frecuencia','tipoIndicador.descripcion AS tipoIndicador',
    					'unidadMedida.descripcion AS unidadMedida','entregable.descripcion AS entregable',
    					'entregableTipo.descripcion AS entregableTipo','entregableAccion.descripcion AS entregableAccion');
	}

	public function scopeMostrarDatos($query){
    	$query->select('proyectoComponentes.id','proyectoComponentes.idEntregable','idEntregableTipo', 
    					'idEntregableAccion','idUnidadMedida','indicador','entregable.descripcion AS entregable',
						'entregableTipo.descripcion AS entregableTipo','entregableAccion.descripcion AS entregableAccion',
						'unidadMedida.descripcion AS unidadMedida')
    			->leftjoin('catalogoEntregables AS entregable','entregable.id','=','idEntregable')
    			->leftjoin('catalogoEntregablesTipos AS entregableTipo','entregableTipo.id','=','idEntregableTipo')
    			->leftjoin('catalogoEntregablesAcciones AS entregableAccion','entregableAccion.id','=','idEntregableAccion')
    			->leftjoin('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','idUnidadMedida');
    }

    public function registroAvance(){
    	return $this->hasMany('RegistroAvanceMetas','idNivel')->where('nivel','=',1);
    }

    public function planMejora(){
    	return $this->hasMany('EvaluacionPlanMejora','idNivel')->where('nivel','=',1);
    }

    public function planesMejoraJurisdiccion(){
    	return $this->hasMany('PlanMejoraJurisdiccion','idNivel')->where('nivel','=',1);
    }

    public function accion(){
    	return $this->hasOne('Accion','idComponente');
    }
	
	public function desglose(){
		return $this->hasMany('ComponenteDesglose','idComponente');
	}

	public function desgloseMunicipios(){
		return $this->hasMany('ComponenteDesglose','idComponente')->listarMunicipios();
	}
	
	public function desgloseConDatos(){
		return $this->hasMany('ComponenteDesglose','idComponente')->listarDatos();
	}

	public function desgloseCompleto(){
		return $this->hasMany('ComponenteDesglose','idComponente')->listarDatos()->with('metasMes');
	}

	public function actividades(){
		return $this->hasMany('Actividad','idComponente')->with('usuario');
	}

	public function actividadesDescripcion(){
		return $this->hasMany('Actividad','idComponente')->conDescripcion();
	}

	public function metasMes(){
		return $this->hasMany('ComponenteMetaMes','idComponente');
	}

	public function metasMesJurisdiccion(){
		return $this->hasMany('ComponenteMetaMes','idComponente')->agrupadoJurisdiccion();
	}

	public function metasMesAgrupado(){
		return $this->hasMany('ComponenteMetaMes','idComponente')->agrupadoMes();
	}

	public function usuario(){
		return $this->belongsTo('SentryUser','actualizadoPor');
	}

	public function formula(){
		return $this->belongsTo('Formula','idFormula');
	}

	public function dimension(){
		return $this->belongsTo('Dimension','idDimensionIndicador');
	}

	public function frecuencia(){
		return $this->belongsTo('Frecuencia','idFrecuenciaIndicador');
	}

	public function tipoIndicador(){
		return $this->belongsTo('TipoIndicador','idTipoIndicador');
	}

	public function unidadMedida(){
		return $this->belongsTo('UnidadMedida','idUnidadMedida');
	}

	public function comportamientoAccion(){
		return $this->belongsTo('ComportamientoAccion','idComportamientoAccion');
	}
	
	public function tipoValorMeta(){
		return $this->belongsTo('TipoValorMeta','idTipoValorMeta');
	}

	public function entregable(){
		return $this->belongsTo('Entregable','idEntregable');
	}

	public function entregableTipo(){
		return $this->belongsTo('EntregableTipo','idEntregableTipo');
	}

	public function entregableAccion(){
		return $this->belongsTo('EntregableAccion','idEntregableAccion');
	}
	public function comentarios(){
    	return $this->hasMany('EvaluacionComentario','idElemento')->where('tipoElemento','=',2);
    }
	
	public function observaciones(){
		return $this->hasMany('ObservacionRendicionCuenta','idElemento')->where('nivel','=',1);
	}
}