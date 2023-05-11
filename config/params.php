<?php

$bitrix = require __DIR__ . '/bitrix_params.php';
$createTaskParams = require __DIR__ . '/create_task_params.php';

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'bitrix' => $bitrix,
    'createTask' => $createTaskParams,
];
