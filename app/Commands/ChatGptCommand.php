<?php

namespace App\Commands;

use App\Helpers\Curl;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChatGptCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('gpt')
            ->setDescription('Chat with GPT-3');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = config('openai_api');
        $question = config('gpt_question');
        while (true) {
            $text = ask($input, $output, $this, $question);
            if ($text === 'exit') {
                $output->writeln('Bye!');
                break;
            }
            $curl = new Curl();
            $data = ['data' => ['message' => $text]];
            try {
                $response = $curl->postJson($url, $data);
            } catch (Exception $e) {
                $output->writeln($e->getMessage());
                break;
            }
            $response = json_decode($response);
            $output->writeln($response->result->choices[0]->text);
        }
        return Command::SUCCESS;
    }

}