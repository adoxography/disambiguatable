<?php

namespace Adoxography\Disambiguatable;

use Illuminate\Database\Eloquent\Model;

class Disambiguation extends Model
{
    protected $fillable = [
        'disambiguatable_type',
        'disambiguatable_id',
        'disambiguator'
    ];

    protected $casts = [
        'disambiguator' => 'int'
    ];
}
