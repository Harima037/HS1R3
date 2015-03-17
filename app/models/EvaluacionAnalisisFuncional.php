<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionAnalisisFuncional extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionAnalisisFuncional";
}