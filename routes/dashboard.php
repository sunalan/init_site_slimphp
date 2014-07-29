<?php
$app->get('(/)', function() use ( $app ) {
	$app->render('test.phtml');
});