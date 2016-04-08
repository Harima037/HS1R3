<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class IndicadorFASSAMetaTrimestre extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "indicadorFASSAMetaTrimestre";
}