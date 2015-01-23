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
}