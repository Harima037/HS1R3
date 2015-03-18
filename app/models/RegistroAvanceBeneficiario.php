<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class RegistroAvanceBeneficiario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "registroAvancesBeneficiarios";

	/*public function scopeAcumulado(){
		return $query->select('')
					->groupBy();
	}*/
}