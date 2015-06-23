<?php

namespace Invoice\Response;

interface ResponseInterface
{
    public function isOk();

    public function getErrorMessage();
}
