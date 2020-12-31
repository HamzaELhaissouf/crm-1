<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Operation extends JsonResource
{
    public function toArray($request)
    {
        return [
            "" . $this->created_at => $this->montant
        ];
        // return parent::toArray($this);
    }
}
