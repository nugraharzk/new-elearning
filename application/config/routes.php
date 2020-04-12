<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Default Route
$route['default_controller'] = "welcome";
$route['404_override'] = '';

// My Route
$route['register'] = 'login/register';
$route['login'] = 'login/index';
$route['lupa_password'] = 'login/lupa_password';
$route['pp'] = 'login/pp';
$route['login_log'] = 'login/login_log';
$route['logout'] = 'login/logout';
$route['orangtua'] = 'orangtua/index';


/* End of file routes.php */
/* Location: ./application/config/routes.php */