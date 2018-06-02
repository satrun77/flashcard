<?php

Route::group([
    'middleware' => 'api',
    'namespace'  => 'Moo\\FlashCard\\Controller',
    'prefix'     => 'api',
], function (\Illuminate\Routing\Router $router) {
    $router->get('/cards', 'ApiController@getCards');
    $router->get('/card/{id}', 'ApiController@getCard')->where('id', '[0-9]+');
    $router->get('/categories', 'ApiController@getCategories');
});
