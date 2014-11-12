<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Entregable extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoEntregables";
}