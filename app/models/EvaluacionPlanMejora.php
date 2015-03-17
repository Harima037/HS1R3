<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionPlanMejora extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionPlanMejora";
}