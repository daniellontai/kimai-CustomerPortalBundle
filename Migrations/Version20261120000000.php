<?php

/*
 * This file is part of the "Customer-Portal plugin" for Kimai.
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace KimaiPlugin\CustomerPortalBundle\Migrations;

use App\Doctrine\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

final class Version20261120000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optional customer portal labels';
    }

    public function up(Schema $schema): void
    {
        $schema->getTable('kimai2_customer_portals')->addColumn('label', Types::STRING, [
            'length' => 255,
            'notnull' => false,
        ]);
    }

    public function down(Schema $schema): void
    {
        $schema->getTable('kimai2_customer_portals')->dropColumn('label');
    }
}
