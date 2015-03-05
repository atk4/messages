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

        $from = $this->add(
            $this->config->getTypeModelClassName($this->current_row['from_type'])
        )->tryLoad($this->current_row['from_id']);

        $to = $this->add(
            $this->config->getTypeModelClassName($this->current_row['to_type'])
        )->tryLoad($this->current_row['to_id']);

        $mixed_html = '';

        $message_model = $this->config->getMessageModelClassName();

        $mixed_html = $mixed_html .
            '<div class="'. $this->config->get('message-from-class') .'"><strong>From:</strong> ' .
                $message_model::getFromTypes($this->current_row['from_type']) . ' ' . $from['name'] .
                ($this->current_row['from_is_deleted']?'(deleted)':'') .
            '</div>'
        ;
        $mixed_html = $mixed_html .
            '<div class="'. $this->config->get('message-to-class') .'"><strong>To:</strong> ' .
                $message_model::getToTypes($this->current_row['to_type']) . ' ' . $to['name'] .
                ($this->current_row['to_is_deleted']?'(deleted)':'') .
            '</div>'
        ;


        $mixed_html = $mixed_html .
            '<div class="'. $this->config->get('message-text-class') .'">' . $this->current_row['text'] . '</div>';




        $this->current_row_html['message'] = $mixed_html;

        parent::formatRow();

    }

}