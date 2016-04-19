<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionProyectoObservacion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionProyectoObservaciones";
}