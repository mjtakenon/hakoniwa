<?php

namespace App\Services\Hakoniwa;

interface JsonEncodable
{
    public function toJson(): string;
    public static function fromJson(string $json): JsonEncodable;
}
