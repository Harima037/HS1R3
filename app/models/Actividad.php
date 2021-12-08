<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Actividad extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteActividades";

	public function scopeContenidoCompleto($query){
		return $query->with('formula','dimension','frecuencia','tipoIndicador','unidadMedida');
	}

	public function scopeConDescripcion($query){
		return $query->leftjoin('catalogoFormulas AS formula','formula.id','=','componenteActividades.idFormula')
					->leftjoin('catalogoDimensionesIndicador AS dimension','dimension.id','=','componenteActividades.idDimensionIndicador')
					->leftjoin('catalogoFrecuenciasIndicador AS frecuencia','frecuencia.id','=','componenteActividades.idFrecuenciaIndicador')
					->leftjoin('catalogoTiposIndicadores AS tipoIndicador','tipoIndicador.id','=','componenteActividades.idTipoIndicador')
					->leftjoin('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','componenteActividades.idUnidadMedida')
					->leftjoin('catalogoComportamientosAccion AS comportamiento','comportamiento.id','=','componenteActividades.idComportamientoAccion')
					->leftjoin('catalogoTiposValorMeta AS tipoValorMeta','tipoValorMeta.id','=','componenteActividades.idTipoValorMeta')
    				->select('componenteActividades.*','formula.descripcion AS formula','dimension.descripcion AS dimension',
    					'frecuencia.descripcion AS frecuencia','tipoIndicador.descripcion AS tipoIndicador',
    					'unidadMedida.descripcion AS unidadMedida','comportamiento.descripcion AS comportamientoDescripcion','tipoValorMeta.descripcion AS tipoValorMetaDescripcion');
	}
	
	public function scopeMostrarDatos($query){
    	$query->select('componenteActividades.id','idUnidadMedida','indicador',
						'unidadMedida.descripcion AS unidadMedida','componenteActividades.idComponente')
    			->leftjoin('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','idUnidadMedida');
    }

    public function accion(){
    	return $this->hasOne('Accion','idActividad');
    }
	
	public function registroAvance(){
    	return $this->hasMany('RegistroAvanceMetas','idNivel')->where('nivel','=',2);
    }

    public function planMejora(){
    	return $this->hasMany('EvaluacionPlanMejora','idNivel')->where('nivel','=',2);
    }

    public function planesMejoraJurisdiccion(){
    	return $this->hasMany('PlanMejoraJurisdiccion','idNivel')->where('nivel','=',2);
    }
	
	public function desglose(){
		return $this->hasMany('ActividadDesglose','idActividad');
	}

	public function desgloseMunicipios(){
		return $this->hasMany('ActividadDesglose','idActividad')->listarMunicipios();
	}
	
	public function desgloseConDatos(){
		return $this->hasMany('ActividadDesglose','idActividad')->listarDatos();
	}

	public function desgloseCompleto(){
		return $this->hasMany('ActividadDesglose','idActividad')->listarDatos()->with('metasMes');
	}

	public function metasMes(){
		return $this->hasMany('ActividadMetaMes','idActividad');
	}

	public function metasMesAgrupado(){
		return $this->hasMany('ActividadMetaMes','idActividad')->agrupadoMes();
	}

	public function metasMesJurisdiccion(){
		return $this->hasMany('ActividadMetaMes','idActividad')->agrupadoJurisdiccion();
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

	public function comentarios(){
    	return $this->hasMany('EvaluacionComentario','idElemento')->where('tipoElemento','=',3);
    }
	
	public function observaciones(){
		return $this->hasMany('ObservacionRendicionCuenta','idElemento')->where('nivel','=',2);
	}
}