<?php

namespace App\Payments;

class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public array $data = [], // optional data (like payment link, IDs...)
        public int $status = 200
    ) {}


    public static function success(string $message, array $data = []): self
    {
        return new self(true, $message, $data);
    }

    public static function failure(string $message, array $data = [], int $status = 422): self
    {
        return new self(false, $message, $data, $status);
    }
}
