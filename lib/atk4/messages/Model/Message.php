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

    protected static $from_types = ['admin'=>'Administrator'];
    protected static $to_types = ['admin'=>'Administrator','broadcast'=>'Broadcast message'];

    function init(){
        parent::init();

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
            if(!$m['from_id']) throw $m->exception('from_id is required','MissedData');
            if(!$m['from_type']) throw $m->exception('from_type is required','MissedData');
            if(!$m['to_type']) throw $m->exception('to_type is required','MissedData');
            if(!$m['text']) throw $m->exception('text is required','MissedData');
            if(!in_array($m['from_type'],static::getFromTypes())) throw $m->exception('Incorrect from_type value','IncorrectData');
            if(!in_array($m['to_type'],static::getToTypes())) throw $m->exception('Incorrect to_type value','IncorrectData');
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
}