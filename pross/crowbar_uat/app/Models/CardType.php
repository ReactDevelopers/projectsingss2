<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;

class CardType extends Model
{
	protected $table = 'card_type';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';

    protected $fillable = ['id', 'type', 'name', 'image', 'status', 'updated', 'created'];
}
