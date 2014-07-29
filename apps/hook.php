<?php

$app->hook('slim.before', function() use ($app) {

	$out_of_service = $app->config('out_of_service');

	if ($out_of_service) {
		$app->render('out_of_service.phtml', array(), 403);
		$app->stop();
	}
});

$app->hook('json.dispatch', function() use ($app) {
    $app->response->header('Content-Type', 'application/json');
    $app->response->header('Pragma', 'no-cache');
    $app->response->header('Cache-Control', 'no-cache, private, no-store, must-revalidatei, pre-check=0, post-check=0, max-age=0, max-stale=0');
    $app->response->header('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
    $app->response->header('Vary', '*');
    $app->response->header('Last-Modified', gmdate("D, d M Y H:i:s")." GMT");
});