<?php

declare(strict_types=1);

namespace Tomaj\InvoiceClient\Transport;

enum HttpMethod: string
{
    case Get = 'GET';
    case Post = 'POST';
    case Delete = 'DELETE';
}
