<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'otp',
        'otp_expires_at',
        'refresh_token',
        'refresh_token_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function userExams(): HasMany
    {
        return $this->hasMany(UserExam::class);
    }

    public function userExamsTest(): HasMany
    {
        return $this->hasMany(UserExamTest::class);
    }

    public function answers(): HasManyThrough
    {
        return $this->hasManyThrough(UserAnswer::class, UserExam::class, 'user_id', 'user_exam_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function userGroups(): BelongsToMany
    {
        return $this->belongsToMany(UserGroup::class, 'user_group_users', 'user_id', 'user_group_id');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (string)$this->id,
            'title' =>  $this->name??'',
            'email' => $this->email,
            'phone' => $this->phone,
            'translit' => \App\Helpers\TransliteratorHelper::transliterate($this->first_name . ' ' . $this->last_name),
        ];
    }
}
