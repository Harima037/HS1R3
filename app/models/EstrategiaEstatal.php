<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EstrategiaEstatal extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoEstrategiasEstatales";
}