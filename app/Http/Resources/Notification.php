<?php

namespace App\Http\Resources;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Notification extends JsonResource
{
    public function toArray($request)
    {
        return [
            'date'=>Carbon::now()->format('d-m-Y') , 
            'message' => "l ' état de  produit :  " . $this->designation . "est critique ",
            'stock_actuel' => $this->stock_actuel
    ];
        // return parent::toArray($this);
    }
}
