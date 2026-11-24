<?php

/**
 * A fake chcemfakturu.sk API for the examples, run by the PHP built-in web server:
 *
 *     FAKE_API_STATE=/tmp/state.json php -S 127.0.0.1:8701 examples/fake-api/server.php
 *
 * It answers the requests InvoiceApi sends with the response format the client expects:
 * {"status": "ok", ...} or {"status": "error", "message": "..."}. The create response follows the
 * documented API; the detail and delete formats are assumptions, not verified against the live API.
 * Invoices are kept in the JSON file FAKE_API_STATE, so ids are deterministic: inv-0001, inv-0002, ...
 *
 * The only accepted token is "test-token". A create request without "Content-Type: application/json"
 * is rejected with 415, so the examples prove that GuzzleTransport sends the header.
 * Prefixes simulate failures:
 *   /outage/...  answers 502 with an HTML page, like a proxy in front of a broken server
 *   /flaky/...   answers 503 to the first two requests, then works
 *   /slow/...    waits 3 seconds before it answers
 */

declare(strict_types=1);

const TOKEN = 'test-token';
const CURRENCIES = ['EUR', 'USD', 'CZK', 'GBP', 'HUF', 'PLN', 'RUB'];

$stateFile = getenv('FAKE_API_STATE') ?: sys_get_temp_dir() . '/fake-invoice-api.json';
$state = is_file($stateFile) ? json_decode((string) file_get_contents($stateFile), true) : null;
if (!is_array($state)) {
    $state = ['next' => 1, 'invoices' => [], 'flaky' => 0];
}

$method = $_SERVER['REQUEST_METHOD'];
$path = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = 'http://' . $_SERVER['HTTP_HOST'];

/**
 * @param array<string, mixed> $body
 */
function json(int $status, array $body): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function error(int $status, string $message): never
{
    json($status, ['status' => 'error', 'message' => $message]);
}

if (str_starts_with($path, '/outage/')) {
    http_response_code(502);
    header('Content-Type: text/html');
    echo "<html><body><h1>502 Bad Gateway</h1></body></html>\n";
    exit;
}

if (str_starts_with($path, '/flaky/')) {
    $state['flaky']++;
    file_put_contents($stateFile, json_encode($state));
    if ($state['flaky'] <= 2) {
        error(503, 'Service temporarily unavailable');
    }
    $path = substr($path, strlen('/flaky'));
}

if (str_starts_with($path, '/slow/')) {
    sleep(3);
    $path = substr($path, strlen('/slow'));
}

// links of an invoice, opened by people, not by the client
if (preg_match('~^/(html|pdf|pdf-download)/([^/]+)$~', $path, $m) === 1) {
    $id = rawurldecode($m[2]);
    if (!isset($state['invoices'][$id])) {
        http_response_code(404);
        echo "Invoice not found\n";
        exit;
    }
    if ($m[1] === 'html') {
        header('Content-Type: text/html; charset=utf-8');
        echo '<h1>' . htmlspecialchars((string) ($state['invoices'][$id]['name'] ?? $id)) . "</h1>\n";
    } else {
        header('Content-Type: application/pdf');
        echo "%PDF-1.4 fake invoice {$id}\n";
    }
    exit;
}

if (!str_starts_with($path, '/api/v1/invoices/')) {
    error(404, 'Unknown endpoint');
}

$authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if ($authorization !== 'Bearer ' . TOKEN) {
    error(401, 'Invalid API token');
}

if ($path === '/api/v1/invoices/create') {
    if ($method !== 'POST') {
        error(405, 'Method not allowed');
    }
    $contentType = strtolower(trim(explode(';', (string) ($_SERVER['CONTENT_TYPE'] ?? ''))[0]));
    if ($contentType !== 'application/json') {
        error(415, 'The request body must be sent with Content-Type: application/json');
    }
    $request = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($request) || !is_array($request['invoice'] ?? null)) {
        error(400, 'The request body must be {"invoice": {...}}');
    }
    $invoice = $request['invoice'];
    if (($invoice['items'] ?? []) === []) {
        error(422, 'The invoice has no items');
    }
    if (isset($invoice['currency']) && !in_array($invoice['currency'], CURRENCIES, true)) {
        error(422, 'Unsupported currency');
    }
    $id = sprintf('inv-%04d', $state['next']++);
    $state['invoices'][$id] = ['id' => $id] + $invoice;
    file_put_contents($stateFile, json_encode($state));
    json(200, [
        'status' => 'ok',
        'id' => $id,
        'html_url' => $base . '/html/' . $id,
        'download_pdf_url' => $base . '/pdf-download/' . $id,
    ]);
}

if (preg_match('~^/api/v1/invoices/(detail|delete)/([^/]+)$~', $path, $m) === 1) {
    $id = rawurldecode($m[2]);
    $expected = $m[1] === 'detail' ? 'GET' : 'DELETE';
    if ($method !== $expected) {
        error(405, 'Method not allowed');
    }
    if (!isset($state['invoices'][$id])) {
        error(404, 'Invoice not found');
    }
    if ($m[1] === 'detail') {
        json(200, ['status' => 'ok', 'invoice' => $state['invoices'][$id]]);
    }
    unset($state['invoices'][$id]);
    file_put_contents($stateFile, json_encode($state));
    json(200, ['status' => 'ok']);
}

error(404, 'Unknown endpoint');
