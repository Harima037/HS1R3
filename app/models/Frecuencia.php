<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Frecuencia extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoFrecuenciasIndicador";
}