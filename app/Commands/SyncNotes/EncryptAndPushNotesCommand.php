<?php

declare(strict_types=1);

namespace App\Commands\SyncNotes;

use App\Services\ProgressBarBuilder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class EncryptAndPushNotesCommand extends BaseSyncCommand
{
    private OutputInterface $output;

    protected function configure(): void
    {
        $this->setName('notes:push')
            ->setDescription('Push encrypted notes to the server.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;
        try {
            $this->temporaryDecryptFiles();
        } catch (Throwable $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return self::FAILURE;
        }

        $this->encryptFilesWithLog();
        $this->removeTemporaryFiles();
        $this->pushToOriginRepository();
        $output->writeln('<info>Notes encrypted and pushed successfully.</info>');
        return self::SUCCESS;
    }

    private function encryptFilesWithLog(): void
    {
        $progressBar = (new ProgressBarBuilder($this->output))->build();
        $filesToEncrypt = $this->getFilesToEncrypt();
        $progressBar->setMessage('<info>Starting encrypt files...</info>', 'status');
        foreach ($progressBar->iterate($filesToEncrypt) as $fileToEncrypt) {
            $progressBar->setMessage('<info>Encrypting file ' . $fileToEncrypt . '</info>', 'status');
            $extractedFilePath = explode($this->getDecryptedNotesPath(), $fileToEncrypt)[1];
            $this->gpg->encryptFile($fileToEncrypt, $this->getEncryptedNotesPath() . $extractedFilePath);
        }
        $progressBar->clear();
    }

    /**
     * @throws Throwable
     */
    private function temporaryDecryptFiles(): void
    {
        $encryptedFiles = $this->scanEncryptedFilesRecursive();
        $progressBar = (new ProgressBarBuilder($this->output))->build();
        $progressBar->setMessage('<info>Starting...</info>', 'status');
        foreach ($progressBar->iterate($encryptedFiles) as $encryptedFile) {
            $progressBar->setMessage('<info>Checking file ' . $encryptedFile . '</info>', 'status');
            $encryptedFilePathFromEncryptedNotesPath = explode(
                $this->getEncryptedNotesPath(),
                $encryptedFile
            )[1];
            $temporaryDecryptedFilePath = $this->getTmpPath() . $encryptedFilePathFromEncryptedNotesPath;
            $this->gpg->decryptFile($encryptedFile, $temporaryDecryptedFilePath);
        }
        $progressBar->clear();
    }

    private function getFilesToEncrypt(): array
    {
        $decryptedFiles = $this->scanDecryptedFilesRecursive();
        $filesToEncrypt = [];
        $progressBar = (new ProgressBarBuilder($this->output))->build();
        $progressBar->setMessage('<info>Starting...</info>', 'status');
        foreach ($progressBar->iterate($decryptedFiles) as $decryptedFile) {
            $extractedFilePath = explode($this->getDecryptedNotesPath(), $decryptedFile)[1];
            if (!$this->isIdenticalFiles($this->getTmpPath() . $extractedFilePath, $decryptedFile)) {
                $filesToEncrypt[] = $decryptedFile;
                $progressBar->setMessage('</info>File to encrypt: ' . $decryptedFile . '</info>', 'status');
            }
        }
        $progressBar->clear();
        return $filesToEncrypt;
    }

    private function isIdenticalFiles(string $file1, string $file2): bool
    {
        if (!file_exists($file1) || !file_exists($file2)) {
            return false;
        }
        return file_get_contents($file1) === file_get_contents($file2);
    }

    private function removeTemporaryFiles(): void
    {
        exec(
            sprintf("rm -rf %s", escapeshellarg($this->getTmpPath()))
        ) && mkdir($this->getTmpPath());
    }

    private function pushToOriginRepository(): void
    {
        $projectPath = $this->getRepoPath();
        $command = "cd $projectPath && git add " . $this->getEncryptedNotesPath();
        $command .= " && git commit -m 'Encrypted notes' && git push origin main";
        exec($command);
    }
}