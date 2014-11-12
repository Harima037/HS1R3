<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProgramaSectorial extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoProgramasSectoriales";
}