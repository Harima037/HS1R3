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

    public function accion(){
    	return $this->hasOne('Accion','idComponente');
    }
	
	public function desglose(){
		return $this->hasMany('ComponenteDesglose','idComponente');
	}
	
	public function desgloseCompleto(){
		return $this->hasMany('ComponenteDesglose','idComponente')->listarDatos()->with('metasMes');
	}

	public function actividades(){
		return $this->hasMany('Actividad','idComponente')->with('usuario');
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

	public function entregable(){
		return $this->belongsTo('Entregable','idEntregable');
	}

	public function entregableTipo(){
		return $this->belongsTo('EntregableTipo','idEntregableTipo');
	}

	public function entregableAccion(){
		return $this->belongsTo('EntregableAccion','idEntregableAccion');
	}
}