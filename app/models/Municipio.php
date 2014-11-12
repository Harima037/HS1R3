<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Municipio extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaMunicipios";
}