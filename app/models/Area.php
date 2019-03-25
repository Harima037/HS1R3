<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Area extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaAreas";
}