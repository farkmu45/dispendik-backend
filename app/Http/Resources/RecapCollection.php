<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RecapCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'institution' => $request->user()->institution->name,
            'activities' => $this->collection->transform(
                fn ($activity) => [
                    'name' => $activity->name,
                    'date' => $activity->date,
                    'description' => $activity->description,
                ]
            ),
        ];
    }
}
