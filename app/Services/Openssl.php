<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\CipherInterface;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\BadFormatException;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\File;
use Defuse\Crypto\Key;
use Random\RandomException;
use RuntimeException;

final class Openssl implements CipherInterface
{
    private Key $key;

    public function __construct()
    {
        try {
            $this->key = Key::loadFromAsciiSafeString(file_get_contents(config('openssl')['key_path']));
        } catch (BadFormatException|EnvironmentIsBrokenException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function encryptData(string $text): false|string
    {
        try {
            return Crypto::encrypt($text, $this->key);
        } catch (EnvironmentIsBrokenException) {
            throw new RuntimeException('Environment is broken');
        }
    }

    public function decryptData(string $text): false|string
    {
        try {
            return Crypto::decrypt($text, $this->key);
        } catch (EnvironmentIsBrokenException|WrongKeyOrModifiedCiphertextException $e) {
            throw new RuntimeException($e->getMessage());
        }
    }

    public function encryptFile(string $fromPath, string $toPath): false|int
    {
        return force_file_put_contents($toPath, $this->encryptData(file_get_contents($fromPath)));
    }

    public function decryptFile(string $fromPath, string $toPath): false|int
    {
        return force_file_put_contents($toPath, $this->decryptData(file_get_contents($fromPath)));
    }
}