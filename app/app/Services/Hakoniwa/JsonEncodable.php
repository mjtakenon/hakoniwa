<?php

namespace App\Services\Hakoniwa;

interface JsonEncodable
{
    public function toJson(): string;
    public function fromJson(string $json): JsonEncodable;
}
