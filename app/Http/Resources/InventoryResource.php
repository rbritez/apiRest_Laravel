<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $inventory = $this->resource;

        return [
            "warehouseId"=> $inventory->warehouseId,
            "warehouseName"=> $inventory->warehouseName,
            "totalQuantity" => $inventory->totalQuantity,
            "reservedQuantity"=> $inventory->reservedQuantity,
            "hasUnlimitedQuantity"=> $inventory->hasUnlimitedQuantity,
        ];
    }
}