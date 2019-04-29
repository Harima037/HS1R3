<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CatalogoCargo extends BaseModel
{
	use SoftDeletingTrait;
	protected $dates = ['borradoAl'];
	protected $table = "catalogosSSA.Cargo";
}