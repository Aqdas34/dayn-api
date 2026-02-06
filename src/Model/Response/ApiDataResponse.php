<?php

namespace App\Model\Response;

class ApiDataResponse
{
    private string $message;
    private mixed $data;

    public function __construct(
        mixed $data,
        string $message = "Successful",
    )
    {
        $this->data = $data;
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): ApiDataResponse
    {
        $this->message = $message;
        return $this;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): ApiDataResponse
    {
        $this->data = $data;
        return $this;
    }

    public static function fromData(mixed $data, $message = "Successful"): ApiDataResponse
    {
        return new self($data, $message);
    }
}