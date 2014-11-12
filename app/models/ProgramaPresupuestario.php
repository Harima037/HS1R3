<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProgramaPresupuestario extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoProgramasPresupuestales";
}