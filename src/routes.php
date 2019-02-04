<?php

use Controllers\CalendarController;
use Controllers\LoginController;
use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/login', LoginController::class . ':index');
$app->get('/calendar', CalendarController::class . ':index');
$app->get('/calendar/add', CalendarController::class . ':add');
