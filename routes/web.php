<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/', 'NumerosController@index');

Route::post('/guardarNumero', 'NumerosController@guardarNumeroWeb');

Route::post('/guardarURL', 'NumerosController@guardarURL');
//Route::get('/borraURL', 'NumerosController@borrarURL');
Route::post('/borraURL/{id}/', [
    'as' => 'borraURL', 'uses' => 'NumerosController@borrarURL']);
Route::get('/suma', 'NumerosController@retornaSumaNumeros');

Route::post('/llamarASuma', 'NumerosController@LlamarServidoresYSumar');

