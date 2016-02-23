<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionPlanMejora extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionPlanMejora";

	public function scopeConteo($query){
		return $query->groupBy('idProyecto')
					->groupBy('mes')
					->select('idProyecto','mes',DB::raw('count(mes) AS planesMejora'));
	}
}