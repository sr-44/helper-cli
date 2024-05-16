<?php


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;

if (!function_exists('config')) {
    function config($key): mixed
    {
        static $config;
        if (!$config) {
            $config = require __DIR__ . '/../../config.php';
        }
        return $config[$key] ?? null;
    }
}

if (!function_exists('ask')) {
    function ask(InputInterface $input, OutputInterface $output, Command $command, string $question): string
    {
        $helper = $command->getHelper('question');
        $question = new Question($question);
        return $helper->ask($input, $output, $question);
    }
}

function force_file_put_contents(string $pathWithFileName, mixed $data, int $flags = 0): false|int
{
    $dirPathOnly = dirname($pathWithFileName);
    if (!file_exists($dirPathOnly)) {
        mkdir($dirPathOnly, 0775, true);
    }
    return file_put_contents($pathWithFileName, $data, $flags);
}