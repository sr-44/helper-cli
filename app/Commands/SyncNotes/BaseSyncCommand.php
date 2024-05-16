<?php

declare(strict_types=1);

namespace App\Commands\SyncNotes;

use App\Contracts\CipherInterface;
use App\Services\Openssl;
use App\Services\ScanDirs;
use Symfony\Component\Console\Command\Command;

abstract class BaseSyncCommand extends Command
{
    protected ScanDirs $scanDirs;
    protected CipherInterface $cipher;

    public function __construct()
    {
        parent::__construct();
        $this->cipher = new Openssl();
        $this->scanDirs = new ScanDirs();
    }

    protected function getEncryptedNotesPath(): string
    {
        return config('sync_notes')['encrypted_notes_path'];
    }

    protected function getDecryptedNotesPath(): string
    {
        return config('sync_notes')['decrypted_notes_path'];
    }

    protected function scanEncryptedFilesRecursive(): array
    {
        return $this->scanDirs->list_dir($this->getEncryptedNotesPath())->files;
    }

    protected function scanDecryptedFilesRecursive(): array
    {
        return $this->scanDirs->list_dir($this->getDecryptedNotesPath())->files;
    }

    protected function scanFilesRecuirsive(): array
    {
        return $this->scanDirs->list_dir(dirname(__DIR__, 3))->files;
    }

    protected function getTmpPath(): string
    {
        return config('sync_notes')['tmp_path'];
    }

    protected function getRepoPath(): string
    {
        return dirname($this->getEncryptedNotesPath());
    }

}