<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Jurisdiccion extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "vistaJurisdicciones";
}