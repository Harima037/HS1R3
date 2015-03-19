<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionComentario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionComentarios";
}