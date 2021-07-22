<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ObjetivoEstrategico extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoObjetivosEstrategicos";

	public function objetivoPED(){
		return $this->belongsTo('ObjetivoPED','idObjetivoPED');
	}
}