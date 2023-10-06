<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        "warehouseId",
        "warehouseName",
        "totalQuantity",
        "reservedQuantity",
        "hasUnlimitedQuantity"
    ];
}
