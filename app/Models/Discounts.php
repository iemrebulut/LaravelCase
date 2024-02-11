<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discounts extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $guarded = [];

    public function order() {
        return $this->belongsTo(Orders::class, 'orderId');
    }
}
