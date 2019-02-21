<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RegistroAvanceEstrategiaComentario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "registroAvancesEstrategiaComentario";
	
	
}