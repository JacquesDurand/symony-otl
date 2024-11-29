<?php

declare(strict_types=1);

namespace App\Message;

final class DemoMessage
{
    public function __construct(
        public string $message = '' {
            get  => strtoupper($this->message);
        }
        )
    {
    }
}