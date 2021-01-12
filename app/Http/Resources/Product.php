<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class Product extends JsonResource
{
    public function toArray($request)
    {
        return [
            'designation' => $this->designation,
            'sell_quatity' => DB::table('operations')->where('type', 'sell')->where('product_id', $this->id)->sum('quantity')
        ];
        // return parent::toArray($this);
    }
}
