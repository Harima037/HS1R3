<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProgramaComentario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programaComentarios";
	
}