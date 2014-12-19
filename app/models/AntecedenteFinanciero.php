<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class AntecedenteFinanciero extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "fibapAntecedentesFinancieros";
}