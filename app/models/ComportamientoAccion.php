<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ComportamientoAccion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoComportamientosAccion";
}