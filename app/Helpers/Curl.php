<?php

namespace App\Helpers;

use CurlHandle;
use Exception;

class Curl
{
    private false|CurlHandle $curl;

    public function __construct()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
    }

    /**
     * @throws Exception
     */
    public function get(string $url, array $options = []): bool|string
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $options);
        return $this->exec();
    }

    public function postJson($url, $data)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        return $this->exec();
    }

    /**
     * @throws Exception
     */
    public function post(string $url, array $options = []): bool|string
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $options);
        return $this->exec();
    }

    /**
     * @throws Exception
     */
    private function exec(): bool|string
    {
        $response = curl_exec($this->curl);
        $error = curl_error($this->curl);
        if ($error) {
            throw new Exception($error);
        }
        return $response;
    }


    public function __destruct()
    {
        curl_close($this->curl);
    }
}