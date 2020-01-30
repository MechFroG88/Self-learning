<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/**  
 * Users  
 */
$route['user']['POST']                       = 'User/create';
$route['user/login']['POST']                 = 'User/login';
$route['user']['GET']                        = 'User/get';
$route['user/logout']['POST']                = 'User/logout';
$route['user/delete/(:num)']['POST']         = 'User/delete/$1';

/**  
 * Classes 
 */
$route['class']['POST']                      = 'Classes/create';
$route['class']['GET']                       = 'Classes/get';                     
$route['class/(:num)']['GET']                = 'Classes/get_single/$1';
$route['class/(:num)']['POST']               = 'Classes/update/$1';
$route['class/delete/(:num)']['POST']        = 'Classes/delete/$1';
$route['class/find']['POST']                  = 'Classes/find';
