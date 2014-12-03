<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Region extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaRegiones";
}