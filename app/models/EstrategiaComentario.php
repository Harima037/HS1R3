<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EstrategiaComentario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "estrategiaComentarios";
	
}