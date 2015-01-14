<?php
class FibapDatosComponente extends BaseModel
{
	protected $table = "fibapDatosComponente";

	public function accion(){
        return $this->belongsTo('Accion','idAccion');
    }
}