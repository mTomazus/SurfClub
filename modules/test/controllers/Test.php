<?php
class Test extends Trongate {

    function index() {

        $this->module('trongate_tokens');
        $token = $this->trongate_tokens->_attempt_get_valid_token();


        if ($token === false) {
            redirect('tg-admin');
        }
        $data['token'] = $token;
        $data['view_file'] = 'test';
        $this->template('public', $data);

    }
    function port() {

        $data['token'] = $token;
        $data['view_file'] = 'port';
        $this->template('public', $data);

    }
    function baltija() {
        $data['view_file'] = 'baltija';
        $this->template('public', $data);
    }
    function dragdrop() {
        $data['view_file'] = 'dragdrop';
        $this->template('public', $data);
    }
}