<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * @var string
     */
    protected $table = 'article';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id', 'link', 'title', 'subtitle', 'slug', 'content'
    ];

    public function categories(){
        return $this->belongsToMany(Category::class);
    }

    public function images(){
        return $this->belongsToMany(Image::class);
    }
}
