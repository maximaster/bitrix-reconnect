<?php

declare(strict_types=1);

namespace Maximaster\BitrixReconnect\Mysqli;

use Bitrix\Main\DB\ConnectionException;
use Bitrix\Main\DB\MysqliConnection;
use Maximaster\BitrixReconnect\Exception\ConnectionRetryLimitExceededException;

/**
 * Соединение, которое пытается себя восстановить после получения ошибки про
 * превышение количества подключений.
 */
class RecoverableMysqliConnection extends MysqliConnection
{
    private int $timeout;
    private int $totalRetries;

    public function __construct(array $configuration)
    {
        parent::__construct($configuration);

        $this->timeout = (int) ($configuration['timeout'] ?? 1);
        $this->totalRetries = (int) ($configuration['totalRetries'] ?? 10);
    }

    /**
     * {@inheritDoc}
     *
     * @throws ConnectionRetryLimitExceededException
     */
    protected function connectInternal(): void
    {
        $this->tryToConnect();
    }

    /**
     * @throws ConnectionRetryLimitExceededException
     */
    private function tryToConnect(int $attempt = 1): void
    {
        // Подключить stechstudio/backoff, если нужен более сложный алгоритм.
        if ($attempt > $this->totalRetries) {
            throw new ConnectionRetryLimitExceededException(
                $this,
                'Превышено количество попыток подключения к базе данных.'
            );
        }

        try {
            parent::connectInternal();
        } catch (ConnectionException $exception) {
            sleep($this->timeout);
            $this->tryToConnect(++$attempt);
        }
    }
}
