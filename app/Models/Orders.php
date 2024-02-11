<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at'];

    protected $guarded = [];

    public function discounts() {
        return $this->hasMany(Discounts::class, 'orderId');
    }
}
