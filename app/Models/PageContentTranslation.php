<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContentTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'page_content_id',
        'locale',
        'title',
        'slug',
        'content',
    ];
}
