<?php defined('SYSPATH') or die('No direct script access.');
defined('EVENTCONFIG_VERSION') OR define('EVENTCONFIG_VERSION', '2.0.8');

Kohana::$config->load('adm')
    ->set('eventtype', array(
        'title' => 'Типы событий',
        'url' => 'eventConfig',
        'icon' => 'fa-calendar',
        'order' => 110,
    ));