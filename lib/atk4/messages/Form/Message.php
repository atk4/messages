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
        $field = $this->addField('autocomplete\Form_Field_Basic','to_id')->setCaption('To');
        return $field;
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
        $field = $this->addField('autocomplete\Form_Field_Basic','from_id')->setCaption('From');
        return $field;
    }


    /**
     * Adds reload autoload field on select option in drop down field.
     * Autoload field will send additional parameter together with search request which
     * will help to pick up proper model to search in.
     *
     * You can use Config singleton of this add-on to match your custom types which mut be described in
     * your custom message class inherited from atk4\messages\Model_Message.
     *
     *     Config::getInstance()->setTypeModelClassName('writer','Model_Writer');
     *
     * @param \Form_Field_DropDown $to_field
     * @param \autocomplete\Form_Field_Basic $reload_field
     */
    private function addReloadOnChange(\Form_Field_DropDown $to_field, \autocomplete\Form_Field_Basic $reload_field) {

        $to_field->selectnemu_options = array(
            'change' => $this->js(null,'function() {'.
                    $this->js()->atk4_messages()->changeAutocompleteURL(
                        $to_field->name,
                        $reload_field->name,
                        $reload_field->other_field->name,
                        $to_field->short_name
                    )
                    .'}')
        );

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
            $to_type = $this->model['to_type']?:$this->config->getDefaultToMessateType();
        }

        if ($to_model = $this->config->getTypeModelClassName($to_type)) {
            $this->to_id_field->setModel($to_model);
            $this->to_id_field->set($this->model['to_id']);
        }

        $this->to_type_field->set($to_type);



        // get form url (if field reload)
        if ($p = $_GET[$this->from_type_field->short_name]) {
            $from_type = $p;
        }
        // get from form (on form submit)
        else if ($p = $_POST[$this->from_type_field->name]) {
            $from_type = $p;
        }
        // get from model (on form generation)
        else {
            $from_type = $this->model['from_type']?:$this->config->getDefaultFromMessateType();
        }

        if ($from_model = $this->config->getTypeModelClassName($from_type)) {
            $this->from_id_field->setModel($from_model);
            $this->from_id_field->set($this->model['from_id']);
        }

        $this->from_type_field->set($from_type);

    }

}