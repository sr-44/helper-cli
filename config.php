<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

return [
    'openai_api' => $_ENV['API_URL'],
    'gpt_question' => 'You: ',
    'todo_file' => __DIR__ . '/todo.txt',
    'gpg' => [
        'public_key' => __DIR__ . '/' . $_ENV['PUBLIC_KEY'],
        'private_key' => __DIR__ . '/' . $_ENV['PRIVATE_KEY'],
        'passphrase' => $_ENV['PASSPHRASE'],
        'fingerprint' => $_ENV['FINGERPRINT'],
        'encrypted_notes_path' => $_ENV['ENCRYPTED_NOTES_PATH'],
        'decrypted_notes_path' => $_ENV['DECRYPTED_NOTES_PATH'],
        'tmp_path' => $_ENV['TMP_PATH'],
        'files_for_ignore' => [
            '.',
            '..',
            '.git',
            'vendor',
        ],
    ],
];