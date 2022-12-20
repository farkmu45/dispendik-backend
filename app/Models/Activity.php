<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public static function boot()
    {
        parent::boot();

        static::updated(function (Activity $model) {
            if ($model->picture != $model->getOriginal('picture')) {
                Storage::delete('public/' . $model->getOriginal('picture'));
            }
        });

        static::deleted(function (Activity $model) {
            Storage::delete('public/' . $model->picture);
        });
    }
}
