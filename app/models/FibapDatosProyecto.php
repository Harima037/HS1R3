<?php
class FibapDatosProyecto extends BaseModel
{
	protected $table = "fibapDatosProyecto";

	public function fibap(){
        return $this->belongsTo('FIBAP','idFibap');
    }
}