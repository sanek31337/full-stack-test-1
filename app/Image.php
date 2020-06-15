<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * @var string
     */
    protected $table = 'image';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public $timestamps = false;

    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_id', 'link', 'media_type', 'url', 'type', 'slug', 'source', 'width', 'height', 'caption',
        'copyright', 'credit', 'published', 'modified'
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
