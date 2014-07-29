<?php

class Session extends \Slim\Middleware {

    public function call() {
        $app = $this->app;
        !SessionNative::started() && SessionNative::start();
        SessionNative::touch();
        $this->next->call();
    }
}
