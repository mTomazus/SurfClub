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
        $data['vanduo'] = $this->vanduo();
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

    function vanduo() {

        // API URL for Klaipėdos Jūrų Uosto VMS
        $url = 'https://api.meteo.lt/v1/hydro-stations/klaipedos-juru-uosto-vms/observations/measured/latest';

        // Fetch data
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Use true in production
        $response = curl_exec($ch);
        curl_close($ch);

        // Decode JSON
        $data = json_decode($response, true);

        // Get latest observation
        $latestTemp = null;
        $latestTime = null;

        if ($data && isset($data['observations'])) {
            // Get the last entry (latest time is at the end)
            $latest = end($data['observations']);
            $latestTemp = $latest['waterTemperature'];
            $latestTime = $latest['observationTimeUtc'];
        }

        return $latestTemp;

    }

}