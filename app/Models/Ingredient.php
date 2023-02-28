<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    // Setting stock_utilized_quantity and low_stock_warning_sent will be used to update utilized stocks record and keep email sent status
    protected $fillable = ['stock_utilized_quantity', 'low_stock_warning_sent'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients');
    }
}
