<?php

declare(strict_types=1);

namespace Maximaster\BitrixReconnect\Exception;

use Exception;
use Maximaster\BitrixReconnect\Mysqli\RecoverableMysqliConnection;
use Throwable;

class ConnectionRetryLimitExceededException extends Exception
{
    private RecoverableMysqliConnection $connection;

    public function __construct(
        RecoverableMysqliConnection $connection,
        $message = '',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->connection = $connection;
    }

    public function getConnection(): RecoverableMysqliConnection
    {
        return $this->connection;
    }
}
