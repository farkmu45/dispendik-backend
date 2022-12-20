<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserWithoutTokenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth,
            'phone_number' => $this->phone_number,
            'institution_id' => $this->institution_id ?? null,
            'institution' => $this->institution->name ?? null,
            'role_id' => $this->role_id,
        ];
    }
}
