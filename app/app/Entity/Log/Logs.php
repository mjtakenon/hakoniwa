<?php

namespace App\Entity\Log;

class Logs
{
    private array $logs = [];

    public static function create(): static
    {
        return new static;
    }

    public function add(LogRow $log): static
    {
        $this->logs[] = $log;
        return $this;
    }

    public function unshift(LogRow $log): static
    {
        array_unshift($this->logs, $log);
        return $this;
    }

    public function merge(Logs $logs): static
    {
        $this->logs = array_merge($this->logs, $logs->getLogs());
        return $this;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}
