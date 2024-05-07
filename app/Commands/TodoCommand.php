<?php

namespace App\Commands;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TodoCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('todo')
            ->setDescription('List all of the tasks on your todo list.')
            ->addArgument('add', InputArgument::OPTIONAL, 'Add a new task to the list.')
            ->addArgument('check', InputArgument::OPTIONAL, 'Check a task off the list.')
            ->addArgument('count', InputArgument::OPTIONAL, 'Count of task');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->getFormatter()->setStyle('success', new OutputFormatterStyle('green', options: ['bold']));
        $output->getFormatter()->setStyle('error', new OutputFormatterStyle('red', options: ['bold']));
        $output->getFormatter()->setStyle('info', new OutputFormatterStyle('yellow', options: ['bold']));
        $action = $input->getArgument('add') ?? $input->getArgument('check');

        switch ($action) {
            case 'add':
                $output->writeln('<info>Adding a new task</>');
                $text = ask($input, $output, $this, 'Task: ');
                if ($this->addTask($text)) {
                    $output->writeln('<success>Task added</>');
                } else {
                    $output->writeln('<error>Error adding task</>');
                    return Command::FAILURE;
                }
                break;
            case 'check':
                $tasks = $this->checkTask();
                if (!empty($tasks)) {
                    $this->printTasks($output, $tasks);
                } else {
                    return Command::SUCCESS;
                }
                break;
            case 'remove':
                $output->writeln('<info>Removing a task</>');
                $tasks = $this->checkTask();
                if (!empty($tasks)) {
                    $this->printTasks($output, $tasks);
                } else {
                    return Command::SUCCESS;
                }
                $taskNumber = ask($input, $output, $this, 'Task number: ');
                try {
                    $this->removeTask($taskNumber, $tasks);
                    $output->writeln('<success>Task removed</>');
                } catch (Exception $e) {
                    $output->writeln('<error>' . $e->getMessage() . '</>');
                    return Command::FAILURE;
                }
                break;
            case 'count':
                $count = count(file(config('todo_file')));
                $output->writeln($count);
                break;
            default:
                $output->writeln('<error>Unknown command</>');
                return Command::INVALID;
        }
        return Command::SUCCESS;
    }


    private function addTask(string $task): false|int
    {
        return file_put_contents(config('todo_file'), $task . PHP_EOL, FILE_APPEND);
    }

    /**
     * @throws Exception
     */
    private function removeTask(int $taskNumber, array $tasks): void
    {
        if (isset($tasks[$taskNumber - 1])) {
            unset($tasks[$taskNumber - 1]);
            file_put_contents(config('todo_file'), implode(PHP_EOL, $tasks));
        } else {
            throw new RuntimeException('Task not found');
        }
    }

    private function checkTask(): false|array
    {
        return file(config('todo_file'), FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
    }

    private function printTasks(OutputInterface $output, array $tasks): void
    {
        $output->writeln('<info>Tasks:</>');
        foreach ($tasks as $key => $task) {
            $output->writeln(($key + 1) . '. ' . $task);
        }
    }
}
