<?php

namespace App\Services;

use CurlHandle;
use Exception;
use RuntimeException;

class Curl
{
    private false|CurlHandle $curl;

    public function __construct()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, true);
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

    /**
     * @throws Exception
     */
    public function postJson($url, $data): bool|string
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data, JSON_THROW_ON_ERROR));
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
        $error = curl_errno($this->curl);
        if ($error) {
            throw new RuntimeException(curl_error($this->curl));
        }
        return $response;
    }


    public function __destruct()
    {
        curl_close($this->curl);
    }
}