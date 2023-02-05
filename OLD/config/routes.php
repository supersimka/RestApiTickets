<?php
use Pecee\{
    Http\Request,
    SimpleRouter\SimpleRouter as Router
};
 
Router::setDefaultNamespace('app\controllers');
Router::get('/', 'ProjectController@index');

 ?>
