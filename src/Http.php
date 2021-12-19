<?php

namespace Aqayepardakht\Http;

class Http {
    private $result     = null;
    private $statusCode = null;
    private $headers    = [];
    private $params     = [];
    private $message    = '';
    private $url        = '';

    public function get($url, $params = []) {
        $this->setParams($params);
        return $this->instance($url, 'GET');
    }

    public function post($url, $params = []) {
        $this->setParams($params);
        return $this->instance($url, 'POST');
    }

    protected function instance($url, $methode = 'GET') {
        $this->url = $url;

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POSTFIELDS     => http_build_query($this->params),
            CURLOPT_HTTPHEADER     => $this->headers,
            CURLOPT_CUSTOMREQUEST  => $methode
        ]);

        $this->result = curl_exec($ch);

        if (curl_errno($ch)) {
            $this->message = curl_error($ch);
        }

        $this->statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $this;
    }

    public function body() {
        return $this->result;
    }

    public function json() {
        return json_decode($this->result);
    }

    public function status() {
        return $this->statusCode;
    }

    public function delParam($key, $value) {
        $this->params[$key] = $value;
    }

    public function appendParams($params) {
        $this->params = array_merge($params, $this->params);
        return $this;
    }

    public function appendParam($key, $value) {
        $this->params[$key] = $value;
    }

    public function setParams($params) {
        $this->params = $params;
        return $this;
    }

    public function setHeaders($headers) {
        $this->headers = $headers;
        return $this;
    }

    public function appendHeaders($header, $value) {
        $this->headers[$header] = $value;
        return $this;
    }

    public function isSuccess() {
        return ($this->status() >= 200 && $this->status() < 300);
    }

    public function isFailed() {
        return ($this->status() >= 400);
    }

    public function getMessage() {
        return $this->message;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function withToken($token) {
        $this->appendHeaders('Authorization: Bearer', $token);
        return $this;
    }
}
