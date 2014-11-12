<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Dimension extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoDimensionesIndicador";
}