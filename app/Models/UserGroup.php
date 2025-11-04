<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class UserGroup extends Model
{
    use Searchable;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_group_users', 'user_group_id', 'user_id');
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->attributes['created_at']
                ? \Carbon\Carbon::parse($this->attributes['created_at'])->format('d-m-Y')
                : null,
        );
    }

    public function toSearchableArray(): array
    {

        return [
            'id' => (string)$this->id,
            'title' =>  $this->title??'',
            'created_at' => $this->created_at,
        ];
    }
}
