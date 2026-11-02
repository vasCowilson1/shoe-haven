<?php

namespace Tests\Unit;

use App\Database\Connectors\NeonPostgresConnector;
use InvalidArgumentException;
use Tests\TestCase;

class NeonPostgresConnectorTest extends TestCase
{
    public function test_it_adds_the_neon_endpoint_to_the_connection_dsn(): void
    {
        $connector = new class extends NeonPostgresConnector
        {
            public function dsn(array $config): string
            {
                return $this->getDsn($config);
            }
        };

        $dsn = $connector->dsn([
            'host' => 'ep-example-pooler.us-east-1.aws.neon.tech',
            'database' => 'neondb',
            'port' => 5432,
            'charset' => 'utf8',
            'sslmode' => 'require',
            'neon_endpoint_id' => 'ep-example',
        ]);

        $this->assertStringContainsString("options='endpoint=ep-example'", $dsn);
    }

    public function test_it_leaves_non_neon_connections_unchanged(): void
    {
        $connector = new class extends NeonPostgresConnector
        {
            public function dsn(array $config): string
            {
                return $this->getDsn($config);
            }
        };

        $dsn = $connector->dsn([
            'host' => '127.0.0.1',
            'database' => 'shoe_haven',
            'port' => 5432,
            'charset' => 'utf8',
            'sslmode' => 'prefer',
        ]);

        $this->assertStringNotContainsString('options=', $dsn);
    }

    public function test_it_rejects_malformed_neon_endpoint_ids(): void
    {
        $connector = new class extends NeonPostgresConnector
        {
            public function dsn(array $config): string
            {
                return $this->getDsn($config);
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $connector->dsn([
            'host' => 'ep-example.neon.tech',
            'database' => 'neondb',
            'neon_endpoint_id' => "ep-example';password=invalid",
        ]);
    }
}
