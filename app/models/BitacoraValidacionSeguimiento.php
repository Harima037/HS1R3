<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class BitacoraValidacionSeguimiento extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "bitacoraValidacionSeguimiento";
}