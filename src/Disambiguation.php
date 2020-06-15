<?php
/**
 * Handles the representation of a disambiguation in the database
 *
 * PHP version 7
 *
 * @category Model
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */

namespace Adoxography\Disambiguatable;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a disambiguation in the database
 *
 * @category Model
 * @package  Adoxography\Disambiguatable
 * @author   Graham Still <gstill@uw.edu>
 * @license  MIT (https://github.com/adoxography/disambiguatable/blob/master/LICENSE)
 * @link     https://github.com/adoxography/disambiguatable
 */
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
