<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Response;

/**
 * What every API call returns, whether the API accepted the request or not.
 *
 * A request is OK when the HTTP status is 2xx and the JSON body has "status": "ok".
 * Otherwise getErrorMessage() says why and getStatusCode() / getBody() hold the raw response.
 */
interface ResponseInterface
{
    public function isOk(): bool;

    /**
     * The HTTP status code, 0 when no request was sent (NullTransport).
     */
    public function getStatusCode(): int;

    /**
     * The error message of the API ("message" in the JSON body), or a description of the failure; null when OK.
     */
    public function getErrorMessage(): ?string;

    /**
     * The raw response body, also for errors.
     */
    public function getBody(): string;

    /**
     * The decoded JSON object of the response body, [] when the body is not a JSON object.
     *
     * @return array<mixed>
     */
    public function getData(): array;
}
