<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProgramaArbolObjetivo extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programaArbolObjetivo";
}