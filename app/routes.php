<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', 'HomeController@home');
Route::delete('/todo/{id}', 'HomeController@deleteTodo');
Route::post('/todo', 'HomeController@updateTodo');



//Route::get('/', 'HomeController@showWelcome');

/*Route::get('test', function()
{
	return "meow!";
});*/

Route::get('/test', 'HomeController@showWelcome');
Route::get('/test/{verb?}', 'HomeController@test');
Route::get('form', 'HomeController@form');

Route::post('submit', 'HomeController@submit');
Route::get('submit', function()
{
    echo "GET SUBMIT";
});

/*Route::get('/test/{verb?}', function($verb = 'roaar!')
{
	return "roar: ".$verb;
});*/


Route::get('test/${meow}', function($meow)
{
	echo "meow: ".$meow;
});
