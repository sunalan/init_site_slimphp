<?php

class SessionNative {

    /**
     * @var array
     */
    protected static $settings;
    /**
     * True if the Session is still valid
     *
     * @var boolean
     */
    public static $valid = false;

    /**
     * Error messages for this session
     *
     * @var array
     */
    public static $error = false;

    /**
     * User agent string
     *
     * @var string
     */
    protected static $userAgent = '';

    /**
     * Path to where the session is active.
     *
     * @var string
     */
    public static $path = '/';

    /**
     * Start time for this session.
     *
     * @var integer
     */
    public static $time = false;

    /**
     * Cookie lifetime
     *
     * @var integer
     */
    public static $cookieLifeTime = 3600;

    /**
     * Time when this session becomes invalid.
     *
     * @var integer
     */
    public static $sessionTime = false;

    /**
     * Current Session id
     *
     * @var string
     */
    public static $id = null;

    /**
     * Hostname
     *
     * @var string
     */
    public static $host = null;

    /**
     * Session timeout multiplier factor
     *
     * @var integer
     */
    public static $timeout = null;

    public function __construct() {
    }

    public static function init() {
        self::$time = $_SERVER['REQUEST_TIME'];
        self::$userAgent = $_SERVER['HTTP_USER_AGENT'];
        self::$host = $_SERVER['HTTP_HOST'];

        self::$settings = array_merge(array(
            'session.cookie_secure' => (isset($_SERVER['HTTPS']) ? 1 : 0),
            'session.cookie_lifetime' => self::$cookieLifeTime,
        ));

        self::$sessionTime = self::$time + self::$cookieLifeTime;
    }

    public static function start() {
        if (self::started()) {
            return true;
        }

        self::init();
        $id = self::id();
        session_write_close();
        session_cache_limiter(false);
        self::startStssion();

        if (!$id && self::started()) {
            if (!self::valid()) {
                self::destory();
            }
        }

        self::$error = false;
        return self::started();
    }

    public static function started() {
        return isset($_SESSION) && session_id();
    }

    public static function check($name = null) {
        if (!self::started() && !self::start()) {
            return false;
        }
        if (empty($name) || is_null($name)) {
            return false;
        }

        return isset($_SESSION[$name]);
    }

    public static function id($id = null) {
        if ($id) {
            self::$id = $id;
            session_id(self::$id);
        }
        if (self::started()) {
            return session_id();
        }
        return self::$id;
    }

    public static function delete($name = null) {
        if (self::check($name)) {
            unset($_SESSION[$name]);

            return (self::check($name) === false);
        }
        return false;
    }

    public static function valid() {
        return (self::$userAgent === $_SERVER['HTTP_USER_AGENT'] 
            && $_SERVER['REQUEST_TIME'] <= self::$sessionTime); 
    }

    public static function read($name = null) {
        if (is_null($name)) {
            if (!empty($_SESSION)) {
                return $_SESSION;
            }
            return false;
        }
        if (self::check($name)) {
            return $_SESSION[$name];
        }

        return null;
    }

    public static function touch() {
        if (self::started()) {
            self::$time = $_SERVER['REQUEST_TIME'];
            self::$sessionTime = self::$time + self::$cookieLifeTime;
        }
    }

    public static function write($name = null, $value = null) {
        if (is_null($name) || empty($name)) {
            return false;
        }

        if (is_array($name)) {
            $write = $name;
            foreach($write as $key => $name) {
                if (is_string($value)) {
                    $_SESSION[$name] = $value;
                } elseif (is_array($value) && isset($value[$key])) {
                    $_SESSION[$name] = $value[$key];
                } else {
                    $_SESSION[$name] = null;
                }
            }
        } else {
            $_SESSION[$name] = $value;
        }
        self::touch();

        return true;
    }

    public static function destroy() {
        if (self::started()) {
            session_destroy();
        }
        self::clear();
    }

    public static function clear() {
        $_SESSION = null;
        self::$id = null;
        self::start();
        self::renew();
    }

    protected static function startStssion() {
        if (headers_sent()) {
            if (empty($_SESSION)) {
                $_SESSION = array();
            }
        } else {
            session_start();
        }
        return true;
    }

    protected static function renew() {
        if (session_id()) {
            if (session_id() !== '' || isset($_COOKIE[session_name()])) {
                setcookie('SlimSession', '', $_SERVER['REQUEST_TIME'] - 42000, self::$path);
            }
            session_regenerate_id(true);
        }
    }
}
