<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Psr\Log\AbstractLogger;
use Tomaj\InvoiceClient\Exception\TransportException;
use Tomaj\InvoiceClient\Invoice\Invoice;
use Tomaj\InvoiceClient\Invoice\InvoiceItem;
use Tomaj\InvoiceClient\InvoiceApi;

/**
 * A minimal PSR-3 logger that prints every record. In an application, pass Monolog or your framework's logger.
 */
final class EchoLogger extends AbstractLogger
{
    /** @var list<string> */
    public array $lines = [];

    public function log($level, string|Stringable $message, array $context = []): void
    {
        // PSR-3 placeholders: "Invoice API error: {error}"
        $text = preg_replace_callback('/\{(\w+)\}/', fn (array $m) => (string) ($context[$m[1]] ?? $m[0]), (string) $message);
        $shown = array_map(
            fn (mixed $value) => $value instanceof Throwable ? $value::class : $value,
            $context,
        );
        $line = sprintf('%-7s %s %s', $level, $text, json_encode($shown, JSON_UNESCAPED_SLASHES));
        $this->lines[] = $line;
        echo $line, "\n";
    }
}

$url = getenv('FAKE_API_URL') ?: 'http://127.0.0.1:8704';
$logger = new EchoLogger();
$token = 'test-token';
$api = new InvoiceApi($token, target: $url, logger: $logger);

$invoice = new Invoice()
    ->setName('Invoice 2026004')
    ->addItem(new InvoiceItem()->setDescription('Consulting')->setQuantity(2)->setPriceItem(50)->setVat(20));

echo "-- a successful call: debug before the request, info after it\n";
$id = (string) $api->createInvoice($invoice)->getId();

echo "-- an API error: warning with the message of the API\n";
$api->getInvoice('inv-9999');

echo "-- no HTTP response: error with the exception, then the exception is thrown\n";
try {
    new InvoiceApi($token, target: 'http://127.0.0.1:9', logger: $logger)->deleteInvoice($id);
} catch (TransportException) {
    echo "(TransportException caught)\n";
}

echo "-- what is never logged\n";
$all = implode("\n", $logger->lines);
echo 'token in the log: ', str_contains($all, $token) ? 'yes' : 'no', "\n";
echo 'invoice data in the log: ', str_contains($all, 'Consulting') ? 'yes' : 'no', "\n";
