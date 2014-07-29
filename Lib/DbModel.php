<?php 

abstract class DBModule {

    protected static $db;
    // Last Insert ID
    protected static $lastid;
    protected static $config = array(
        'connection_string'     => 'sqlite::memory:',
        'error_mode'            => PDO::ERRMODE_EXCEPTION,
        'username'              => null,
        'password'              => null,
        'driver_options'        => null
    );

    public static function configure($key, $value = null) {
        if (is_null($value)) {
            $value = $key;
            $key = 'connection_string';
        }
        self::$config[$key] = $value;
    }

    protected static function setup() {
        if (!is_object(self::$db)) {
            self::$db = new PDO(
                self::$config['connection_string'],
                self::$config['username'],
                self::$config['password'],
                self::$config['driver_options']
            );
            self::$db->setAttribute(PDO::ATTR_ERRMODE, self::$config['error_mode']);
        }
    }
    
    /* ========== Query Data ========== */
    protected static function query( $query = "", $params = array(), $fetchAll = false ) {
        return self::fetch( $query, $params, $fetchAll );
    }
    protected static function query_all( $query = "", $params = array() ) {
        return self::fetch( $query, $params, true );
    }
    protected static function insert( $query = "", $params = array() ) {
        return self::transaction( $query, $params, true );
    }
    protected static function update( $query = "", $params = array() ) {
        return self::transaction( $query, $params );
    }
    protected static function delete( $query = "", $params = array() ) {
        return self::transaction( $query, $params );
    }
    protected static function get_last_insert_id() {
        return self::$lastid;
    }
    /* ========== Fetch Data ========== */
    private static function fetch( $query = "", $params = array(), $fetchAll = false ) {
        self::setup();

        $statement = self::$db->prepare( $query );

        foreach ($params as $key => &$value) {
            $statement->bindParam( $key, $value );    
        }

        $statement->execute();
        
        return $fetchAll === false ? 
               $statement->fetch(PDO::FETCH_ASSOC) :
               $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    /* ========== Transaction Data Table ========== */
    private static function transaction( $query = "", $params = array(), $get_insert_id = false ) {
        self::setup();
    
        try {
            self::$db->beginTransaction();
            $statement = self::$db->prepare( $query );

            foreach ($params as $key => &$value) {
                $statement->bindParam( $key, $value );    
            }

            $result = $statement->execute();
            self::$lastid = $get_insert_id ? self::$db->lastInsertId() : false;
            if ( $result ) {
                self::$db->commit();
                return true;
            } else {
                self::$db->rollBack();
                return false;
            }
        } catch( PDOException $e ) {
            self::$db->rollBack();
            return false;
        }
    }
}