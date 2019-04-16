<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SaleHasProducts;

class Sale extends Model
{
    protected $fillable = ['id_custumer', 'id_seller'];

    public function Products($products){
        return  $products;
    }
}
