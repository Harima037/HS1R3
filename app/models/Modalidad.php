<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Modalidad extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoModalidad";
}