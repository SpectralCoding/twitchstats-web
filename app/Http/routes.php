<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'HomeController@index');
Route::get('channel/{channel}/{accuracy}/{aggressive}', 'DataController@channel');
Route::get('emote/{channel}/{emote}/{accuracy}', 'DataController@emote');
Route::get('topemotes/{channel}/{pastmin}/{count}', 'DataController@topemotes');
Route::get('topchannels/{pastmin}/{count}', 'DataController@topchannels');
Route::get('topchannelforemote/{emote}/{pastmin}/{count}', 'DataController@topchannelforemote');