<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Catalogo extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogo";
}