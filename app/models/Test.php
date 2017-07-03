<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Test extends BaseModel
{
	use SoftDeletingTrait;

	protected $table = "test";
}