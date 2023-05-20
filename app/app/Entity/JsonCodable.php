<?php

namespace App\Entity;

interface JsonCodable
{
    public function toJson(): string;
    public static function fromJson(string $json): JsonCodable;
}
