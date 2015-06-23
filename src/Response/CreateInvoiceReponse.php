<?php

namespace Invoice\Response;

use Invoice\Transport\Result;

class CreateInvoiceResponse implements ResponseInterface
{
    private $id;

    private $downloadPdfUrl;

    private $htmlUrl;

    private $isOk;

    private $errorMessage;

    public function __construct(Result $transportResult)
    {
        if ($transportResult->getCode() != 200) {
            $this->isOk = false;
        }

        $result = json_decode($transportResult->getBody(), true);
        if (!$result) {
            $this->errorMessage = 'Cannot parse response from server';
            return;
        }

        if ($result['status'] != 'ok') {
            $this->isOk = false;
            $this->errorMessage = $result['message'];
        }

        $this->htmlUrl = $result['html_url'];
        $this->downloadPdfUrl = $result['download_pdf_url'];
        $this->id = $result['invoice_id'];
    }

    public function isOk()
    {
        return $this->isOk;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getHtmlUrl()
    {
        return $this->htmlUrl;
    }

    public function getDownloadUrl()
    {
        return $this->downloadPdfUrl;
    }

    public function getId()
    {
        return $this->id;
    }
}