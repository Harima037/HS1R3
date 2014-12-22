<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class DistribucionPresupuesto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapDistribucionPresupuesto";

	public function objetoGasto(){
        return $this->belongsTo('ObjetoGasto','idObjetoGasto');
    }
}