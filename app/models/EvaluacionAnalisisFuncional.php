<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EvaluacionAnalisisFuncional extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "evaluacionAnalisisFuncional";
	
	public function comentarios(){
    	return $this->hasMany('EvaluacionComentario','idElemento')->where('tipoElemento','=',4);
    }
}