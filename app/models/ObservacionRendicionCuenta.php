<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ObservacionRendicionCuenta extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "observacionRendicionCuenta";
}