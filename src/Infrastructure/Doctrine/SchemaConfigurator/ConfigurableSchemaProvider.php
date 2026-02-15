<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\SchemaConfigurator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider;

final readonly class ConfigurableSchemaProvider implements SchemaProvider
{
    public function __construct(
        private Connection $connection,
        private SchemaSubscriber $subscriber,
    ) {
    }

    /**
     * @throws Exception
     */
    #[\Override]
    public function createSchema(): Schema
    {
        $schemaConfig = $this->connection->createSchemaManager()->createSchemaConfig();
        $schema = new Schema([], [], $schemaConfig);
        $configurator = new SchemaConfigurator($schema);
        $this->subscriber->configureSchema($configurator);

        return $schema;
    }
}
