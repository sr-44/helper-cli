<?php

declare(strict_types=1);

namespace App\Commands\SyncNotes;

use App\Services\ProgressBarBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class PullAndDecryptNotesCommand extends BaseSyncCommand
{
    private OutputInterface $output;

    protected function configure(): void
    {
        $this->setName('notes:pull')
            ->setDescription('Pull and decrypt notes from the remote server');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        $this->gullFromOriginRepository();
        $this->decryptFilesWithLog();
        return self::SUCCESS;
    }

    private function gullFromOriginRepository(): void
    {
        $repoPath = $this->getRepoPath();
        exec("cd $repoPath && git pull origin main && git restore --source=HEAD --staged --worktree :/");
    }

    private function decryptFilesWithLog(): void
    {
        $progressBar = (new ProgressBarBuilder($this->output))->build();
        $filesToDecrypt = $this->getFilesToDecrypt();
        $progressBar->setMessage('<info>Starting decrypt files...</info>', 'status');
        foreach ($progressBar->iterate($filesToDecrypt) as $fileToDecrypt) {
            $progressBar->setMessage('<info>Decrypting file ' . $fileToDecrypt . '</info>', 'status');
            $extractedFilePath = explode($this->getEncryptedNotesPath(), $fileToDecrypt)[1];
            $this->cipher->decryptFile($fileToDecrypt, $this->getDecryptedNotesPath() . $extractedFilePath);
        }
        $progressBar->clear();
    }

    private function getFilesToDecrypt(): array
    {
        return $this->scanEncryptedFilesRecursive();
    }
}