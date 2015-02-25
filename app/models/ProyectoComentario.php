<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProyectoComentario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectoComentarios";
	
}