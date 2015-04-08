<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ProgramaArbolProblema extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "programaArbolProblema";
}