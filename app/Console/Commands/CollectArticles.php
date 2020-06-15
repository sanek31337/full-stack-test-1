<?php

namespace App\Console\Commands;

use App\Article;
use App\Category;
use App\Image;
use Carbon\Carbon;
use Eastwest\Json\Json;
use Eastwest\Json\JsonException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use StdClass;

class CollectArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Collecting articles from json file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try
        {
            $articles = $this->getArticles();
            $this->saveArticles($articles);
        }
        catch (\Exception $exception)
        {
            Log::error($exception->getMessage());
        }
    }

    /**
     * @return StdClass
     * @throws FileNotFoundException|JsonException
     */
    private function getArticles()
    {
        if (!Storage::disk('raw_json')->exists('feed.json'))
        {
            throw new FileNotFoundException('There is no feed.json file. Please check it.');
        }

        $content = Storage::disk('raw_json')->get('feed.json');

        try
        {
            return JSON::decode($content);
        }
        catch (JsonException $exception)
        {
            throw $exception;
        }
    }

    private function saveArticles($articles)
    {
        $this->deleteOldData();

        foreach ($articles as $articleObj)
        {
            $categories = [];

            if (isset($articleObj->categories))
            {
                if (isset($articleObj->categories->primary) && !empty($articleObj->categories->primary))
                {
                    $categoryPrimary = Category::firstOrCreate([
                        'name' => $articleObj->categories->primary,
                        'category_type' => Category::CATEGORY_TYPE_PRIMARY
                    ]);

                    $categories[] = $categoryPrimary;
                }

                if (count($articleObj->categories->additional) > 0)
                {
                    foreach ($articleObj->categories->additional as $categoryAdditionalName)
                    {
                        $categoryAdditional = Category::firstOrCreate([
                            'name' => $categoryAdditionalName,
                            'category_type' => Category::CATEGORY_TYPE_ADDITIONAL
                        ]);

                        $categories[] = $categoryAdditional;
                    }
                }
            }

            $images = [];
            if (isset($articleObj->media) && count($articleObj->media) > 0)
            {
                foreach ($articleObj->media as $mediaData)
                {
                    $image = Image::firstOrCreate([
                        'image_id' => $mediaData->media->id,
                        'link' => $mediaData->media->{'@link'},
                        'media_type' => $mediaData->type,
                        'type' => $mediaData->media->type,
                        'slug' => $mediaData->media->slug,
                        'source' => $mediaData->media->source,
                        'url' => $mediaData->media->attributes->url,
                        'width' => $mediaData->media->attributes->width,
                        'height' => $mediaData->media->attributes->height,
                        'caption' => $mediaData->media->attributes->caption,
                        'copyright' => $mediaData->media->attributes->copyright,
                        'credit' => $mediaData->media->attributes->credit,
                        'published' => Carbon::createFromTimeString($mediaData->media->properties->published),
                        'modified' => Carbon::createFromTimeString($mediaData->media->properties->modified)
                    ]);

                    $images[] = $image;
                }
            }

            $article = new Article();
            $article->article_id = $articleObj->id;
            $article->link = $articleObj->{'@link'};
            $article->title = $articleObj->title;
            $article->subtitle = $articleObj->subtitle;
            $article->slug = $articleObj->slug;
            $article->content = $articleObj->content[0]->content;
            $article->content_type = $articleObj->content[0]->type;
            $article->save();
            $article->images()->saveMany($images);
            $article->categories()->saveMany($categories);
            $article->push();
        }
    }

    /**
     * Purge previous articles to make sure that we have fresh articles and other data
     */
    private function deleteOldData()
    {
        DB::table('article')->delete();
        DB::table('category')->delete();
        DB::table('image')->delete();
    }
}
