<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RegistroAvanceEstrategia extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "registroAvancesEstrategia";
	
	public function comentarios(){
		return $this->hasMany('RegistroAvanceEstrategiaComentario','idRegistroAvance');
	}
}