<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 05/03/15
 * Time: 21:35
 */

namespace atk4\messages;

class CRUD_Messages extends \CRUD {

    public $grid_class = 'atk4/messages/Grid_Messages';
    public $form_class = 'atk4/messages/Form_Message';

    function init() {
        $this->form_options = array_merge(
            $this->form_options,
            array('crud' => $this)
        );
        parent::init();
    }

}