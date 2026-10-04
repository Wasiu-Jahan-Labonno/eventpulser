<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    protected static function booted():void
    {
      static::creating(function (Organization $organization){
        if(empty($organization->uuid)){
            $organization->uuid = (string) \Illuminate\Support\Str::uuid(); 
        }
      });
    }
    public function events():HasMany
    {
        return $this->hasMany(Event::class);
    }
}
