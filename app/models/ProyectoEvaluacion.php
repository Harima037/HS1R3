<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProyectoEvaluacion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectosEvaluacion";
}