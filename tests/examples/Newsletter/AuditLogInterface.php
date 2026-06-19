<?php

declare(strict_types=1);

namespace Tests\examples\Newsletter;

interface AuditLogInterface
{
    public function info(string $message): void;

    public function error(string $message): void;
}
