<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Titular extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "titulares";
}