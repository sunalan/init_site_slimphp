<?php

class CsrfGuard extends \Slim\Extras\Middleware\CsrfGuard {

    public function check() {
        // Check sessions are enabled.
        if (session_id() === '') {
            throw new \Exception('Sessions are required to use the CSRF Guard middleware.');
        }

        if (! isset($_SESSION[$this->key])) {
            $_SESSION[$this->key] = sha1(serialize($_SERVER) . rand(0, 0xffffffff));
        }

        $token = $_SESSION[$this->key];

        // Validate the CSRF token.
        if (in_array($this->app->request()->getMethod(), array('POST', 'PUT', 'DELETE'))) {
            $userToken = $this->app->request()->post($this->key);
            if ($token !== $userToken) {
                //$this->app->halt(400, 'Invalid or missing CSRF token.');
                if ($this->app->request()->isAjax()) {
                    $this->app->response()->header('Content-Type', 'application/json');
                    $this->app->render('json.phtml', array(
                        'result' => array('status' => 'err', 'message' => '資料傳輸錯誤。')
                    ), 405);
                } else {
                    $this->app->redirect('/error/405');
                }
            }
        }

        // Assign CSRF token key and value to view.
        $this->app->view()->appendData(array(
            'csrf_key'      => $this->key,
            'csrf_token'    => $token,
        ));
    }
}
