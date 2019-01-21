<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /*
     * Relationships
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'city_id');
    }
}
