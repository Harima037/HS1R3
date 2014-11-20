<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Actividad extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "componenteActividades";
}