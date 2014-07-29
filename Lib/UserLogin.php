<?php

class UserLogin extends \Slim\Middleware {

    public function call() {

        if (session_id() === '') {
            throw new \Exception('Sessions are required to use the User Login middleware.');
        }

        $app = $this->app();

        

        if ($app->request()->isAjax()) {
            $app->response()->header('Content-Type', 'application/json');
            $app->render('json.phtml', array(
                'result' => array('state' => 'err', 403)
            ));
        }

        $this->next->call();
    }
}