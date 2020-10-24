<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'profile' => 'array',
    ];

    /**
     * A user may be assigned many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function rolesName()
    {
        return Cache::remember("uid-{$this->id}-rolesName", config('session.lifetime') * 60, function () {
            return $this->roles->pluck('name');
        });
    }

    /**
     * Assign a new role to the user.
     *
     * @param  mixed  $role
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::whereName($role)->firstOrCreate(['name' => $role]);
        }

        $this->roles()->syncWithoutDetaching($role);

        Cache::put("uid-{$this->id}-abilities", $this->roles->map->abilities->flatten()->pluck('name')->unique(), config('session.lifetime') * 60);
        Cache::put("uid-{$this->id}-rolesName", $this->roles->pluck('name'), config('session.lifetime') * 60);
    }

    /**
     * Fetch the user's abilities.
     *
     * @return array
     */
    public function abilities()
    {
        return Cache::remember("uid-{$this->id}-abilities", config('session.lifetime') * 60 , function () {
            return $this->roles->map->abilities->flatten()->pluck('name')->unique();   
        });
    }
}
