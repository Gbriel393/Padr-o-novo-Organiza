<?php
define(
    'BANCO_API_URL',
    getenv('BANCO_API_URL') ?: ''
);

define(
    'BANCO_CLIENT_ID',
    getenv('BANCO_CLIENT_ID') ?: ''
);

define(
    'BANCO_CLIENT_SECRET',
    getenv('BANCO_CLIENT_SECRET') ?: ''
);

define(
    'BANCO_WEBHOOK_SECRET',
    getenv('BANCO_WEBHOOK_SECRET') ?: ''
);

?>