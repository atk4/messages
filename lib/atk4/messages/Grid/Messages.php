<?php
/**
 * Created by PhpStorm.
 * User: vadym
 * Date: 05/03/15
 * Time: 22:14
 */


namespace atk4\messages;

class Grid_Messages extends \Grid {

    protected $config;

    function init() {
        parent::init();
        $this->config = Config::getInstance();
        $this->addColumn('message');
        $this->addFormatter('message','wrap');
    }

    public function renderRows() {
        $this->removeColumn('text');
        $this->removeColumn('from_type');
        $this->removeColumn('to_type');
        $this->removeColumn('from_id');
        $this->removeColumn('to_id');
        $this->removeColumn('from_is_deleted');
        $this->removeColumn('to_is_deleted');
        parent::renderRows();
    }

    function formatRow() {



        /* -------------------------------------
         |
         |   get additional data from DB
         |
         */

        // Get data about sender
        if ( $from_model = $this->config->getTypeModelClassName($this->current_row['from_type']) ) {
            $from = $this->add( $from_model )->tryLoad($this->current_row['from_id']);
        } else {
            $from['name'] = Model_Message::getFromTypes($this->current_row['from_type']);
        }


        // get data about receiver
        if ( $to_model = $this->config->getTypeModelClassName($this->current_row['to_type']) ) {
            $to = $this->add( $to_model )->tryLoad($this->current_row['to_id']);
        } else {
            $to['name'] = Model_Message::getToTypes($this->current_row['to_type']);
        }





        /* -------------------------------------
         |
         |           compile HTML
         |
         */

        $mixed_html = '';

        $message_model = $this->config->getMessageModelClassName();

        $mixed_html = $mixed_html .
            '<div class="'. $this->config->get('message-from-class') .'"><strong>From:</strong> ' .
                $from['name'] . ' (' . $message_model::getFromTypes($this->current_row['from_type']) . ') ' .
                ($this->current_row['from_is_deleted']?'*deleted*':'') .
            '</div>'
        ;
        $mixed_html = $mixed_html .
            '<div class="'. $this->config->get('message-to-class') .'"><strong>To:</strong> ' .
                $to['name'] . ' (' . $message_model::getToTypes($this->current_row['to_type']) . ') ' .
                ($this->current_row['to_is_deleted']?'*deleted*':'') .
            '</div>'
        ;


        $mixed_html = $mixed_html .
            '<div class="'. $this->config->get('message-text-class') .'">' . $this->current_row['text'] . '</div>';


        $this->current_row_html['message'] = $mixed_html;

        parent::formatRow();

    }

}