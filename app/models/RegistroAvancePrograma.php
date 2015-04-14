<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RegistroAvancePrograma extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "registroAvancesPrograma";
	
	public function comentarios(){
		return $this->hasMany('RegistroAvanceComentario','idRegistroAvance');
	}
}