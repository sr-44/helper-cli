<?php


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\Command;

if (!function_exists('config')) {
    function config($key)
    {
        $config = require __DIR__ . '/../../config.php';
        if (isset($config[$key])) {
            return $config[$key];
        }
        return null;
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
