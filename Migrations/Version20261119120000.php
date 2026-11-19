<?php
namespace KimaiPlugin\CustomerPortalBundle\Migrations;
use App\Doctrine\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
final class Version20261119120000 extends AbstractMigration {
 public function getDescription(): string { return 'Add optional date ranges to customer portals'; }
 public function up(Schema $schema): void { $table=$schema->getTable('kimai2_customer_portals'); $table->addColumn('start_date', Types::DATE_IMMUTABLE, ['notnull'=>false]); $table->addColumn('end_date', Types::DATE_IMMUTABLE, ['notnull'=>false]); $table->addColumn('freeze_date_range', Types::BOOLEAN, ['notnull'=>true,'default'=>false]); }
 public function down(Schema $schema): void { $table=$schema->getTable('kimai2_customer_portals'); $table->dropColumn('start_date'); $table->dropColumn('end_date'); $table->dropColumn('freeze_date_range'); }
}
