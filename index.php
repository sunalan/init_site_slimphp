<?php

//$mode = 'staging';
$mode = 'development';
date_default_timezone_set('Asia/Taipei');

if ($mode !== 'production') {
	ini_set('display_errors', 'on');
} else {
	ini_set('display_errors', 'off');
}

require 'vendor/autoload.php';
require 'config.php';

!SessionNative::started() && SessionNative::start();

$app = new \Slim\Slim(array(
	'mode' => $mode,
	'view' => new \Slim\Views\Twig()
));
$app->add(new \Session());
$app->add(new \CsrfGuard('csrf_key'));
$app->setName('');
$app->configureMode( $mode, function () use ( $app, $mode, $APP_SETTINGS, $APP_CONFIG ) {

	$config = array_merge( $APP_SETTINGS[$mode], 
		$APP_CONFIG, 
		array( 'mode' => $mode )
	);

	$app->config( $config );
	$view = $app->view();

	$view->parserExtensions = array(
        //'Twig_Extensions_Extension_Debug',
        'Twig_Extensions_Extension_Text'
    );

	$env = $app->environment();
    /**
     * Replace the QUERY_STRING with Nginx vhost.
     */
    if (preg_match('#(?P<request_uri>[\w/.\-_]+\?)#', $env['QUERY_STRING'], $m)) {
        $query_string = str_replace($m['request_uri'], '', $env['QUERY_STRING']);
        $env->offsetSet('QUERY_STRING', $query_string);
        $path_info = str_replace('?'.$query_string, '', $env['PATH_INFO']);
        $env->offsetSet('PATH_INFO', $path_info);

        $_rewrite_params = explode('&', $query_string);
        foreach($_rewrite_params as $param) {
            $_param = explode('=', $param);
            if (!isset($_GET[$param[0]])) {
                $_GET[(string) $_param[0]] = isset($_param[1]) ? (string) $_param[1] : '';
            }
        }
    }

    $resourceUri = $app->request()->getResourceUri();

	$view->appendData(array(
		'env' => array(
			'mode'	=> $mode,
			'app_name' => $app->getName(),
			'debug' => $APP_SETTINGS[$mode]['debug'],
			'url_scheme' => $env['slim.url_scheme'],
			'resourceUri' => $resourceUri,
			'fullUrl' => $env['slim.url_scheme'].'://'.$env['SERVER_NAME'],
			'rootUri' => $app->request()->getRootUri(),
			'isIE' => (boolean) preg_match('/MSIE/i', $_SERVER['HTTP_USER_AGENT'])
		),
		'session' => array(
			'lang' => SessionNative::check("LANG") ? SessionNative::read("LANG") : 'zh_TW',
			'user_info' => SessionNative::check("USER_INFO") ? SessionNative::read("USER_INFO") : ''
		),
		'app_config' => $APP_CONFIG
	));
	
	$app->view($view);
});

require 'Lib/DbModel.php';
require 'models.php';
require 'routes.php';
require 'apps.php';

$app->error(function (\Exception $e) use ($app) {
	echo $e->getMessage();
});

$app->run();