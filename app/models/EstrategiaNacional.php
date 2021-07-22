<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class EstrategiaNacional extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogoEstrategiasNacionales";
	
}