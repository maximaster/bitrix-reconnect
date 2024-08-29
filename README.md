# maximaster/bitrix-reconnect

Количество доступных подключений к MySQL ограничено. Если для вашего приложения
допустимо подождать освободившегося слота, лишь бы не падать, тогда:

```bash
composer require maximaster/bitrix-reconnect
```

Файл `bitrix/.settings_extra.php`:

```php
use Maximaster\BitrixReconnect\Mysqli\RecoverableMysqliConnection;

require_once $_SERVER['DOCUMENT_ROOT'] . '/../vendor/maximaster/bitrix-reconnect/src/Mysqli/RecoverableMysqliConnection.php';

$connections = (require __DIR__ . '/.settings.php')['connections'];
$connections['value']['default'] = array_merge($connections['value']['default'], [
    'className' => RecoverableMysqliConnection::class,
    'timeout' => 1, // Секунд ожидания после ошибки подключения.
    'totalRetries' => 10, // Количество попыток переподключения.
]);

return ['connections' => $connections];
```

В данном примере, если для текущего хита не найдётся MySQL слотов, то
подключение будет пытаться повториться 10 раз, делая секундую паузу между
попытками.

При исчерпании попыток будет выброшено исключение
`Maximaster\BitrixReconnect\Exception\ConnectionRetryLimitExceededException`.
