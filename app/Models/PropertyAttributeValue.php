<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyAttributeValue extends Model
{
    /*
     * Relationships
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function name()
    {
        return $this->belongsTo(PropertyAttributeName::class, 'property_attribute_name_id');
    }
}
