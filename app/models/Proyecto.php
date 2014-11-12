<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Proyecto extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "proyectos";

	public function beneficiarios(){
		return $this->hasMany('Beneficiario','idProyecto');
	}
}