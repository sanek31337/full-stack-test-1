<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/articles-list', function(Request $request){

    $searchPhrase = $request->get('searchPhrase');
    $categoriesParam = $request->get('categories');
    $categories = null;

    if ($categoriesParam !== null)
    {
        $categories = explode(',', $categoriesParam);
    }

    $articles = \App\Article::whereHas(
        'categories', function($query) use ($categories){
        /** @var \Illuminate\Database\Query\Builder $query */
        $query->when($categories, function($query, $categories){
            $query->whereIn('name', $categories);
        });
    })
        ->with(['categories', 'images'])
        ->when($searchPhrase, function($query, $searchPhrase){
            /** @var \Illuminate\Database\Query\Builder $query */
            return $query->where(function($query) use ($searchPhrase){
                $query->where('title', 'like', '%' . $searchPhrase . '%');
            });
        })->get();

    return $articles->toJson();
});

Route::get('/categories-list', function (Request $request) {

    $categories = DB::table('category')
        ->distinct('name')
        ->get();

    return $categories->map(function ($name) {
        return $name->name;
    })
        ->duplicates()
        ->values()
        ->toJson();
});
