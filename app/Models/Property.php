<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    /*
     * Relationships
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function attributes()
    {
        return $this->hasMany(PropertyAttributeValue::class, 'property_id');
    }
}
