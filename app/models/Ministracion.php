<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Ministracion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "calendarizadoMinistraciones";
}