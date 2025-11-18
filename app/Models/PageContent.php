<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class PageContent extends Model
{
    use Translatable;

    protected $fillable = [
        'code',
        'type',
        'status',
    ];

    public $translatedAttributes = [
        'title',
        'slug',
        'content'
    ];
}
