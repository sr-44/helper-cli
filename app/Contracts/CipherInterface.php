<?php

declare(strict_types=1);

namespace App\Contracts;

interface CipherInterface
{
    public function __construct();

    public function encryptData(string $text): false|string;

    public function decryptData(string $text): false|string;

    public function encryptFile(string $fromPath, string $toPath): false|int;

    public function decryptFile(string $fromPath, string $toPath): false|int;

}