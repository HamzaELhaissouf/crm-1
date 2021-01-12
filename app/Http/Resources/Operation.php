<?php

namespace App\Http\Resources;

use App\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class Operation extends JsonResource
{
    public function toArray($request)
    {
        return ['date'=>$this->created_at->format('m-d-Y') ,'designation'=> $this->product->designation ,'prix_achat'=>$this->prix_achat ,  'quantity'=> $this->quantity  , 'type'=>$this->type ];
        // return parent::toArray($this);
    }
}
