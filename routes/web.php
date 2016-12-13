<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/vote/{voteid}', 'VoteController@getVote');
Route::post('/vote/{voteid}', 'VoteController@postVote');

Route::get('/create', 'VoteController@createVote');
Route::post('/create', 'VoteController@saveVote');

Route::get('/url/{voteid}', 'VoteController@getVoteUrl');

//Route::get('/test', 'TestController@test');
