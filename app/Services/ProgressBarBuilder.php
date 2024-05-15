<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final class ProgressBarBuilder
{
    private ProgressBar $progressBar;

    public function __construct(
        private readonly OutputInterface $output
    ) {
    }

    public function build(): ProgressBar
    {
        $progressBar = new ProgressBar($this->output);
        $this->progressBar = $progressBar;
        $this->setStyle();
        return $progressBar;
    }

    private function setStyle(): void
    {
        $this->progressBar->setBarCharacter('<fg=green>⚬</>');
        $this->progressBar->setEmptyBarCharacter("<fg=red>⚬</>");
        $this->progressBar->setProgressCharacter("<fg=green>➤</>");
        $this->progressBar->setFormat("%status%\n%current%/%max% [%bar%] %percent:3s%%\n");
    }
}