<?php

declare(strict_types=1);

namespace App\Services;

use gnupg;

final class GPG
{
    private gnupg $gpg;

    public function __construct()
    {
        $this->gpg = new gnupg();
        $publicKey = $this->gpg->import($this->getPublicKey());
        $this->gpg->addencryptkey($publicKey['fingerprint']);
        $this->gpg->adddecryptkey($publicKey['fingerprint'], $this->getPassphrase());
        $this->gpg->seterrormode(gnupg::ERROR_EXCEPTION);
    }

    public function encryptFile(string $fromPath, string $toPath): false|int
    {
        return force_file_put_contents($toPath, $this->encryptData(file_get_contents($fromPath)));
    }

    public function decryptFile(string $fromPath, string $toPath): false|int
    {
        return force_file_put_contents($toPath, $this->decryptData(file_get_contents($fromPath)));
    }

    public function encryptData(string $text): false|string
    {
        return $this->gpg->encrypt($text);
    }

    public function decryptData(string $text): false|string
    {
        return $this->gpg->decrypt($text);
    }

    private function getPublicKey(): false|string
    {
        return file_get_contents(config('gpg')['public_key']);
    }

    private function getPassphrase(): string
    {
        return config('gpg')['passphrase'];
    }

}