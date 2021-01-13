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
            'message' =>  $this->designation,
            'stock_actuel' => $this->stock_actuel,
            'etat' => 'faible stock'
    ];
        // return parent::toArray($this);
    }
}
