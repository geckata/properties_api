<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAttributeName extends Model
{
    /*
     * Relationships
     */
    public function values()
    {
        return $this->hasMany(PropertyAttributeValue::class, 'property_attribute_name_id');
    }
}
