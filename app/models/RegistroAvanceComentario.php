<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RegistroAvanceComentario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "registroAvancesProgramaComentario";
	
	
}