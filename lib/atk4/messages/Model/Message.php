<?php
/**
 * Created by PhpStorm.
 * User: konstantin
 * Date: 20.02.15
 * Time: 14:07
 */

namespace atk4\messages;

use \atk4\messages\Trait_DTS;
use \atk4\messages\Trait_RelatedEntities;

class Model_Message extends \SQL_Model {

    use Trait_DTS, Trait_RelatedEntities;

    public $table = 'message';

    protected $config;

    protected static $from_types = ['admin'=>'Administrator'];
    protected static $to_types = ['admin'=>'Administrator','broadcast'=>'Broadcast message'];

    function init(){
        parent::init();

        $this->config = Config::getInstance();

        //$this->addField('subject');
        $this->addField('text')->type('text');
        $this->addField('from_id');
        $this->addField('to_id');
        $this->addField('from_type');
        $this->addField('to_type');
        $this->addField('from_is_deleted')->type('boolean');
        $this->addField('to_is_deleted')->type('boolean');
        $this->addField('is_read')->type('boolean');
        $this->addField('created_dts')->type('datetime');

        $this->createdDTS();

        $this->addHooks();

    }

    function addHooks() {
        $this->addHook('beforeSave',function($m){
            if (!$m['to_type']) throw new \Exception_ValidityCheck('required','to_type');
            if ( $this->config->getTypeModelClassName($m['to_type']) && !$m['to_id'] ) throw new \Exception_ValidityCheck('required','to_id');
            if (!$m['from_type']) throw new \Exception_ValidityCheck('required','from_type');
            if ( $this->config->getTypeModelClassName($m['from_type']) && !$m['from_id'] ) throw new \Exception_ValidityCheck('required','from_id');
            if (!$m['text']) throw new \Exception_ValidityCheck('required','text');
            if (!array_key_exists($m['from_type'],static::getFromTypes())) new \Exception_ValidityCheck('Incorrect value','from_type');
            if (!array_key_exists($m['to_type'],static::getToTypes())) new \Exception_ValidityCheck('Incorrect value','to_type');
        });
    }

    public static function getFromTypes($name=null) {
        $total_array =  array_merge(
            self::$from_types,
            static::$from_types
        );
        if ($name) {
            return $total_array[$name];
        }
        return $total_array;
    }
    public static function getToTypes($name=null) {
        $total_array =  array_merge(
            self::$to_types,
            static::$to_types
        );
        if ($name) {
            return $total_array[$name];
        }
        return $total_array;
    }


    /**
     * Method to create new record with necessary verifications.
     *
     * @param $data
     * @return $this
     * @throws \BaseException
     */
    public function create($data){
        $this->checkLoaded(false);
        $this->set($data);
        $this->save();
        return $this;
    }

    /**
     *
     */
    public function setRead() {
        $this->checkLoaded(true);
        $this->set('is_read',true);
        $this->save();
        return $this;
    }

    /**
     *
     */
    public function deleteForTo() {
        $this->checkLoaded(true);
        $this->set('to_is_deleted',true);
        $this->save();
        return $this;
    }

    /**
     *
     */
    public function deleteForFrom() {
        $this->checkLoaded(true);
        $this->set('from_is_deleted',true);
        $this->save();
        return $this;
    }

    /**
     *
     */
    public function deleteForAll() {
        $this->checkLoaded(true);
        $this->set('to_is_deleted',true);
        $this->set('from_is_deleted',true);
        $this->save();
        return $this;
    }

    /**
     * This very helpful method checks if model->loaded() is satisfying expectation
     *
     * @param bool $loaded
     * @return $this
     * @throws \BaseException
     */
    protected function checkLoaded($loaded = true){
        if($loaded && !$this->loaded()){
            throw $this->exception(get_class($this) . ' must be loaded','NotLoadedModel');
        }else if (!$loaded && $this->loaded()){
            throw $this->exception(get_class($this) . ' must not be loaded','LoadedModel');
        }
        return $this;
    }
}