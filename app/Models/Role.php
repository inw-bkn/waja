<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * A role may assigned to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * A role may have many abilities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function abilities()
    {
        return $this->belongsToMany(Ability::class)->withTimestamps();
    }

    /**
     * Grant the given ability to the role.
     *
     * @param  mixed  $ability
     */
    public function allowTo($ability)
    {
        if (is_string($ability)) {
            $ability = Ability::whereName($ability)->firstOrCreate(['name' => $ability]);
        }

        $this->abilities()->syncWithoutDetaching($ability);

        // NEED to flush new ability to user cache
    }    
}
