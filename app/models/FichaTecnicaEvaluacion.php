<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class FichaTecnicaEvaluacion extends BaseModel{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
    protected $table = "fichaTecnicaEvaluacion";
}