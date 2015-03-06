<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 06/03/15
 * Time: 15:45
 */

namespace atk4\messages;

class Form_Message extends \Form {

    public $config;
    public $crud;

    private $to_type_field;
    private $to_id_field;
    private $from_type_field;
    private $from_id_field;

    function init() {
        parent::init();
        $this->config = Config::getInstance();


        $this->namespace = __NAMESPACE__;

        $public_location = $this->app->pathfinder->addLocation(array(
            'js'=>array( 'packages/' . str_replace(['\\','/'],'_',$this->namespace) . '/js' ),
            'css'=>array( 'packages/' . str_replace(['\\','/'],'_',$this->namespace) . '/css' ),
        ))
            ->setBasePath(getcwd().'/public')
            ->setBaseURL($this->app->getBaseURL())
        ;

        $this->js(true)
            ->_load('atk4_messages')
        ;


        $private_location = $this->app->pathfinder->addLocation(array(
            'addons'=>array('../vendor/atk4'),
        ))->setBasePath('.');
    }



    public function setModel($model, $actual_fields = UNDEFINED) {

        //  get field list form model if no fields provided
        if (!$actual_fields) {
            $actual_fields = $model->getActualFields();
        }

        // to type drop down
        if ($actual_fields && ($key = array_search('to_type', $actual_fields)) !== false) {
            $this->to_type_field = $this->addToTypeField();
            unset($actual_fields[$key]);
        }

        // to autocomplete
        if ($actual_fields && ($key = array_search('to_id', $actual_fields)) !== false) {
            $this->to_id_field = $this->addToField();
            unset($actual_fields[$key]);
        }

        // add reload
        if ($this->to_type_field && $this->to_id_field) {
            $this->addReloadOnChange($this->to_type_field , $this->to_id_field);
        }

        // from type drop down
        if ($actual_fields && ($key = array_search('from_type', $actual_fields)) !== false) {
            $this->from_type_field = $this->addFromTypeField();
            unset($actual_fields[$key]);
        }

        // from autocomplete
        if ($actual_fields && ($key = array_search('from_id', $actual_fields)) !== false) {
            $this->from_id_field = $this->addFromField();
            unset($actual_fields[$key]);
        }

        // add reload
        if ($this->from_type_field && $this->from_id_field) {
            $this->addReloadOnChange($this->from_type_field , $this->from_id_field);
        }

        // it is time to set model to parent
        parent::setModel($model,$actual_fields);

        // hook wich will do actual work with model data and custom added fields
        // this hook will work both on add and edit
        $this->model->addHook('afterLoad',array($this,'setValues'));

        // there is no model hooks if form are in add mode but we still have to do something
        if ($this->crud && $this->crud->virtual_page->isActive() == 'add') $this->setValues();

        // return model (required if form are in CRUD)
        return $this->model;

    }



    /**
     * Generates select field with all available to_types from atk4\message\Model_Message
     *
     * @return Form_Field_DropDown
     */
    private function addToTypeField() {
        $model_class_name = $this->config->getMessageModelClassName();
        $field = $this->addField('DropDown','to_type')->setValueList( $model_class_name::getToTypes() );
        return $field;
    }


    /**
     * Generates autocomplete field connected to related model taken from to_type
     *
     * @return \autocomplete\Form_Field_Basic
     */
    private function addToField() {
        $field = $this->addField('autocomplete\Form_Field_Basic','to');
        return $field;
    }


    private function addReloadOnChange(\Form_Field $to_field, \Form_Field $reload_field) {

        $to_field->selectnemu_options = array(
            'change' => $this->js(null,'function() {'.
                    $this->js()->atk4_messages()->changeAutocompleteURL(
                        $to_field->name,
                        $reload_field->other_field->name,
                        $to_field->short_name
                    )
            .'}')
        );

    }


    /**
     * Generates select field with all available from_types from atk4\message\Model_Message
     *
     * @return Form_Field_DropDown
     */
    private function addFromTypeField() {
        $model_class_name = $this->config->getMessageModelClassName();
        $field = $this->addField('DropDown','from_type')->setValueList( $model_class_name::getFromTypes() );
        return $field;
    }



    /**
     * Generates autocomplete field connected to related model taken from from_type
     *
     * @return \autocomplete\Form_Field_Basic
     */
    private function addFromField() {
        $field = $this->addField('autocomplete\Form_Field_Basic','from');
        return $field;
    }

    public function setValues() {

        // get form url (if field reload)
        if ($p = $_GET[$this->to_type_field->short_name]) {
            $to_type = $p;
        }
        // get from form (on form submit)
        else if ($p = $_POST[$this->to_type_field->name]) {
            $to_type = $p;
        }
        // get from model (on form generation)
        else {
            $to_type = $this->model['to_type'];
        }

        $this->to_id_field->setModel($this->config->getTypeModelClassName($to_type));
        // TODO get name from DB for $this->to_id_field
        $this->to_type_field->set($this->model['to_type']);

        $this->from_id_field->setModel($this->config->getTypeModelClassName($this->model['from_type']));
        $this->from_type_field->set($this->model['from_type']);





//        // get form url (if field reload)
//        if ($p = $_GET['partner_id']) {
//            $partner_id = $p;
//        }
//        // get from form (on form submit)
//        else if ($p = $_POST[$this->partner_field->name]) {
//            $partner_id = $p;
//        }
//        // get from model (on form generation)
//        else {
//            $partner_id = $this->model['partner_id'];
//        }
//        $this->contact_field->model->addCondition('partner_id',$partner_id);
//
//
//        $this->partner_field->set($partner_id);
//        $this->contact_field->set($this->model['contact_id']);

    }

//    public function checkSubmit() {
//
//        if ($this->hasElement('id')) {
//            $this->set('id',$_GET[$this->owner->name.'_id']);
//        }
//
//        $this->model->set($this->get());
//        $this->save();
//
//        $succss_js = array(
//            $this->js()->univ()->successMessage($this->success_message),
//            $this->js()->_selector('#'.$this->crud->name)->trigger('reload'),
//        );
//
//        $this->js(null,$succss_js)->univ()->closeDialog()->execute();
//    }

}