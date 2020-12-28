<?php

use Illuminate\Support\Facades\Route;
use LaravelDomainOriented\Controller\Controller;

Route::get('test', [Controller::class, 'index']);
