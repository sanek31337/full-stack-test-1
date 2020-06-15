<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('category'))
        {
            Schema::create('category', function(Blueprint $table) {
                $table->id()->unsigned()->autoIncrement();
                $table->string('name', 255);
                $table->smallInteger('category_type');
                $table->charset = 'utf8mb4';
                $table->unique(['name', 'category_type']);
            });
        }

        if (!Schema::hasTable('image'))
        {
            Schema::create('image', function(Blueprint $table) {
                $table->id()->unsigned()->autoIncrement();
                $table->uuid('image_id');
                $table->text('link');
                $table->string('media_type', 255);
                $table->string('type', 255);
                $table->string('slug', 255);
                $table->string('source', 255);
                $table->text('url');
                $table->smallInteger('width');
                $table->smallInteger('height');
                $table->string('caption', 255);
                $table->string('copyright', 255);
                $table->string('credit', 255);
                $table->dateTimeTz('published');
                $table->dateTimeTz('modified');
                $table->charset = 'utf8mb4';

                $table->unique(['image_id']);
            });
        }

        if (!Schema::hasTable('article'))
        {
            Schema::create('article', function (Blueprint $table) {
                $table->id()->unsigned()->autoIncrement();
                $table->uuid('article_id')->unique();
                $table->string('link');
                $table->string('title');
                $table->string('subtitle');
                $table->string('slug');
                $table->text('content');
                $table->string('content_type', 255);
                $table->charset = 'utf8mb4';
            });
        }

        if (!Schema::hasTable('article_category'))
        {
            Schema::create('article_category', function(Blueprint $table)
            {
                $table->unsignedBigInteger('category_id');
                $table->unsignedBigInteger('article_id');

                $table->foreign('category_id')->references('id')->on('category');
                $table->foreign('article_id')->references('id')->on('article');
            });
        }

        if (!Schema::hasTable('article_image'))
        {
            Schema::create('article_image', function(Blueprint $table)
            {
                $table->unsignedBigInteger('image_id');
                $table->unsignedBigInteger('article_id');

                $table->foreign('image_id')->references('id')->on('image');
                $table->foreign('article_id')->references('id')->on('article');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('article_image');
        Schema::drop('article_category');
        Schema::drop('article');
        Schema::drop('category');
        Schema::drop('image');
    }
}
