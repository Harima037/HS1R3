<?php
class FibapDatosComponente extends BaseModel
{
	protected $table = "fibapDatosComponente";

	public function accion(){
        return $this->belongsTo('Accion','idAccion');
    }

    public function desgloseComponente(){
        return $this->hasMany('ComponenteDesglose','idAccion','idAccion');
    }

    public function scopeMostrarDatos($query){
    	$query->select('fibapDatosComponente.id','idAccion','idFibap','fibapDatosComponente.idEntregable','idEntregableTipo', 
    					'idEntregableAccion','idUnidadMedida','indicador','entregable.descripcion AS entregable',
						'entregableTipo.descripcion AS entregableTipo','entregableAccion.descripcion AS entregableAccion',
						'unidadMedida.descripcion AS unidadMedida')
    			->leftjoin('catalogoEntregables AS entregable','entregable.id','=','idEntregable')
    			->leftjoin('catalogoEntregablesTipos AS entregableTipo','entregableTipo.id','=','idEntregableTipo')
    			->leftjoin('catalogoEntregablesAcciones AS entregableAccion','entregableAccion.id','=','idEntregableAccion')
    			->leftjoin('catalogoUnidadesMedida AS unidadMedida','unidadMedida.id','=','idUnidadMedida');
    }
}