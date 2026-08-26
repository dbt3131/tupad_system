<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// 1. Root URL route
$route['default_controller'] = 'auth/login';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth Routes
$route['auth/register'] = 'auth/register';
$route['auth/login']    = 'auth/login';
$route['auth/logout']   = 'auth/logout';

// Users Routes
$route['users/create']      = 'users/create';
$route['users/edit/(:num)'] = 'users/edit/$1';
$route['users/delete/(:num)'] = 'users/delete/$1';

// Tupad / Dashboard Route
$route['tupad'] = 'tupad/tupad_list'; // Note: Removed duplicate 'tupad' definition
$route['tupad_payrolls/store'] = 'tupad_payrolls/store';
$route['tupad_payrolls/update/(:num)'] = 'tupad_payrolls/update/$1';