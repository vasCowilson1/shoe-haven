<?php

namespace App\Database\Connectors;

use Illuminate\Database\Connectors\PostgresConnector;
use InvalidArgumentException;

class NeonPostgresConnector extends PostgresConnector
{
    protected function getDsn(array $config)
    {
        $dsn = parent::getDsn($config);
        $endpoint = $config['neon_endpoint_id'] ?? null;

        if ($endpoint === null || $endpoint === '') {
            return $dsn;
        }

        if (! preg_match('/\Aep-[A-Za-z0-9-]+\z/', $endpoint)) {
            throw new InvalidArgumentException('The Neon endpoint ID is invalid.');
        }

        return $dsn.";options='endpoint={$endpoint}'";
    }
}
