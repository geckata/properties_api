<?php

Route::get('properties', 'PropertiesController@index')->name('properties.index');
Route::get('properties/{id}', 'PropertiesController@show')->name('properties.show');
Route::post('properties', 'PropertiesController@store')->name('properties.store');
