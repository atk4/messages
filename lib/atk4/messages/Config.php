<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 05/03/15
 * Time: 23:28
 */

namespace atk4\messages;

class Config {

    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    private $stack = [
        'message-from-class'  => 'atk-label atk-effect-warning atk-block',
        'message-to-class'    => 'atk-label atk-effect-warning atk-block',
        'message-text-class'  => 'atk-swatch-gray atk-box',
    ];

    public function set($key,$value) {
        $this->stack[$key] = $value;
        return $this;
    }
    public function get($key) {
        return $this->stack[$key];
    }




    private $type_model = [
        'admin'     => 'Model_Admin',
        'broadcast' => false,
    ];

    public function setTypeModelClassName($type,$model) {
        $this->type_model[$type] = $model;
        return $this;
    }
    public function getTypeModelClassName($type) {
        return $this->type_model[$type];
    }




    private $message_model = 'atk4\\messages\\Model_Message';

    public function setMessageModelClassName($model_class) {
        $this->message_model = $model_class;
        return $this;
    }
    public function getMessageModelClassName() {
        return $this->message_model;
    }




    private function __construct() {}

}