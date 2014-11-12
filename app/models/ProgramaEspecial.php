<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProgramaEspecial extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoProgramasEspeciales";
}