<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Localidad extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaLocalidades";
}