<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class PropuestaFinanciamiento extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapPropuestaFinanciamiento";

	public function origen(){
		return $this->belongsTo('OrigenFinanciamiento','idOrigenFinanciamiento');
	}
}