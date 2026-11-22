<?php

declare(strict_types=1);

namespace Invoice\Response;

interface ResponseInterface
{
    public function isOk(): bool;

    public function getErrorMessage(): string|false;
}
