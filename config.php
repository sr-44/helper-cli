<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
    'openai_api' => $_ENV['API_URL'],
    'gpt_question' => 'You: ',
];