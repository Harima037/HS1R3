<?php
class FibapDatosComponente extends BaseModel
{
	protected $table = "fibapDatosComponente";

	public function accion(){
        return $this->belongsTo('Accion','idAccion');
    }

    public function desgloceComponente(){
        return $this->hasMany('ComponenteDesgloce','idComponente');
    }
}