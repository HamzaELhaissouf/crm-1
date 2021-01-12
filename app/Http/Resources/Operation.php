<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Operation extends JsonResource
{
    public function toArray($request)
    {
        return ['date'=>$this->created_at->format('Y-m-d') ,  'value'=> $this->quantity * $this->prix_achat , 'type'=>$this->type ];
        // return parent::toArray($this);
    }
}
