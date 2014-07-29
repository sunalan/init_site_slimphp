<?php
if (!defined('DS')) define("DS", DIRECTORY_SEPARATOR);
if (!defined('ROOT')) define("ROOT", dirname(__FILE__));

$COOKIES_SECRET_KEY = 'REWa;sdjfalsdjfa;lskjdfFRWEREDSFFSDGREYTY#@$%FDGERT$Rqwertkuqewioru89w';

$APP_SETTINGS = array(
    'development' => array(
        'mode'          => 'development',
        'debug'         => true,
        'log.level'     => Slim\Log::DEBUG,
        'tmp_dir'       => '/tmp/',
        'logging_dir'   => '/tmp/',
        'convert_cmd'   => '/usr/local/bin/convert',
        'out_of_service'    => false
    ),
    'staging' => array(
        'mode'          => 'staging',
        'debug'         => true,
        'log.level'     => Slim\Log::WARN,
        'tmp_dir'       => '/tmp/',
        'logging_dir'   => '/tmp/',
        'convert_cmd'   => '/usr/bin/convert',
        'out_of_service'    => false
    ),
    'production' => array(
        'mode'          => 'production',
        'debug'         => false,
        'log.level'     => Slim\Log::ERROR,
        'tmp_dir'       => '/tmp/',
        'logging_dir'   => '/tmp/',
        'convert_cmd'   => '/usr/bin/convert',
        'out_of_service'    => false
    )
);

$APP_CONFIG = array(
    'log.enabled'           => true,
    'templates.path'        => './views',
    'cookies.lifetime'      => '30 minutes',
    'cookies.secret_key'    => $COOKIES_SECRET_KEY
);

require 'db_config.php';