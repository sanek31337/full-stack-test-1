<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * @var string
     */
    protected $table = 'category';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = false;

    const CATEGORY_TYPE_PRIMARY = 0;
    const CATEGORY_TYPE_ADDITIONAL = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'category_type'
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
