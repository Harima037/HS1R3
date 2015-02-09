<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Componente extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectoComponentes";
	
	public function scopeContenidoCompleto($query){
		return $query->with('actividades','formula','dimension','frecuencia','tipoIndicador','unidadMedida','entregable',
							'entregableTipo','entregableAccion','desgloce');
	}

	public function desglose(){
		return $this->hasMany('ComponenteDesglose','idComponente');
	}

	public function actividades(){
		return $this->hasMany('Actividad','idComponente')->with('usuario');
	}

	public function metasMes(){
		return $this->hasMany('ComponenteMetaMes','idComponente');
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