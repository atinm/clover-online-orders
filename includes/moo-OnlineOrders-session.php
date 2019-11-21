<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://zaytechapps.com
 * @since      1.0.0
 *
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Moo_OnlineOrders
 * @subpackage Moo_OnlineOrders/includes
 * @author     Mohammed EL BANYAOUI
 */
class MOO_SESSION {

    /**
     * Main Session Instance.
     *
     * Ensures only one instance of Session is loaded or can be loaded.
     *
     * @since 1.3.1
     * @static
     * @return MOO_SESSION - Main instance.
     */
    protected static $_instance = null;
    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $blogId;
    /**
     * @var string
     */
    protected $sessionId;

    /**
     * MOO_SESSION constructor.
     */
    public function __construct()
    {
        $this->type     = 'session';
        $this->blogId   = 'moo_'.get_current_blog_id();
        // if we will us ethe server session, check if not already started start it
        if($this->type === 'session') {
            if(!session_id()) {
                $resultStarting = @session_start();
                if(false === $resultStarting) {
                    //TODO:: what to do when session not started
                } else {
                    $this->sessionId = session_id();
                }
            } else {
                $this->sessionId = session_id();
            }
        }
    }

    /**
     * @return MOO_SESSION|null
     */
    public static function instance() {

        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param $key
     * @param null $key2
     * @return array|null
     */
    function get($key, $key2 = null ){
        if($this->type === 'session') {
            if(isset($_SESSION[$this->blogId][$key])) {
                if(isset($key2)) {
                    if(isset($_SESSION[$this->blogId][$key][$key2])) {
                        return $_SESSION[$this->blogId][$key][$key2];
                    } else {
                        return null;
                    }
                } else {
                    if(isset($key) && isset($_SESSION[$this->blogId][$key]))
                        return $_SESSION[$this->blogId][$key];
                    return null;
                }
            }
            return null;
        }
        return array();
    }

    /**
     * @param $value
     * @param $key
     * @param  $key2
     * @return array
     */
    function set($value, $key, $key2 = null) {
        if($this->type === 'session' && isset($_SESSION)) {
            if(isset($key2)) {
                if(isset($_SESSION[$this->blogId])) {
                    if(isset($_SESSION[$this->blogId][$key]) && is_array($_SESSION[$this->blogId][$key])) {
                        $_SESSION[$this->blogId][$key][$key2] = $value;
                    } else {
                        $_SESSION[$this->blogId][$key] = array(
                            $key2=>$value
                        );
                    }
                } else {
                    $_SESSION[$this->blogId] = array(
                        $key=>array(
                            $key2=>$value
                        )
                    );
                }
                return $_SESSION[$this->blogId][$key][$key2];
            } else {
                if(isset($_SESSION[$this->blogId])) {
                    $_SESSION[$this->blogId][$key] = $value;
                } else {
                    $_SESSION[$this->blogId] = array(
                        $key=>$value
                    );
                }
                return $_SESSION[$this->blogId][$key];
            }

        }
        return array();
    }
    /**
     * @param $key
     * @param null $key2
     * @return true|false
     */
    function delete($key, $key2 = null) {
        if($this->type === 'session' && isset($_SESSION)) {
            if(isset($key2)) {
                unset($_SESSION[$this->blogId][$key][$key2]);
                return true;
            } else {
                unset($_SESSION[$this->blogId][$key]);
                return true;
            }

        }
        return false;
    }

    /**
     * @param $key
     * @param null $key2
     * @return bool
     */
    function exist( $key, $key2 = null ){
        if($this->type === 'session') {
            if(isset($_SESSION[$this->blogId][$key])&& isset($key2)) {
                return isset($_SESSION[$this->blogId][$key][$key2]);
            } else {
                return isset($_SESSION[$this->blogId][$key]);
            }
        }
        return false;
    }
    /**
     * @param $key
     * @param null $key2
     * @return bool
     */
    function isEmpty( $key, $key2 = null ){
        if($this->type === 'session') {
            if(isset($key2)) {
                return empty($_SESSION[$this->blogId][$key][$key2]);
            } else {
                return empty($_SESSION[$this->blogId][$key]);
            }
        }
        return false;
    }
    public function myStartSession() {
        if(!session_id()) {
            @session_start();
        }
    }
    function printDump(){
        if($this->type === 'session') {
            print_r($_SESSION);
        }
    }
}
