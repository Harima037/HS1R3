<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Cobertura extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoCoberturas";
}