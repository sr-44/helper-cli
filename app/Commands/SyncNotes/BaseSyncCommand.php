<?php

declare(strict_types=1);

namespace App\Commands\SyncNotes;

use App\Services\GPG;
use App\Services\ScanDirs;
use Symfony\Component\Console\Command\Command;

abstract class BaseSyncCommand extends Command
{
    protected ScanDirs $scanDirs;
    protected GPG $gpg;

    public function __construct(
    ) {
        parent::__construct();
        $this->gpg = new GPG();
        $this->scanDirs = new ScanDirs();

    }

    protected function getEncryptedNotesPath(): string
    {
        return config('gpg')['encrypted_notes_path'];
    }

    protected function getDecryptedNotesPath(): string
    {
        return config('gpg')['decrypted_notes_path'];
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
        return config('gpg')['tmp_path'];
    }

    protected function getRepoPath(): string
    {
        return dirname($this->getEncryptedNotesPath());
    }

}