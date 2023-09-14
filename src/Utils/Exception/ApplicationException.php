<?php

namespace App\Utils\Exception;

use Exception;

class ApplicationException extends Exception
{
    private string $customMessage;

    public function __construct(string $customMessage) {
        $this->customMessage = $customMessage;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getCustomMessage(): string {
        return $this->customMessage;
    }

    /**
     * @param $customMessage
     */
    public function setCustomMessage($customMessage): void {
        $this->customMessage = $customMessage;
    }
}