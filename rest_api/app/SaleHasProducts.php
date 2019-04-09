<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaleHasProducts extends Model
{
    protected $fillable = ['id_sale', 'id_product'];

}
