<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523203831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE wishlist_product (wishlist_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_4C46D2D7FB8E54CD (wishlist_id), INDEX IDX_4C46D2D74584665A (product_id), PRIMARY KEY(wishlist_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wishlist_product ADD CONSTRAINT FK_4C46D2D7FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist_product ADD CONSTRAINT FK_4C46D2D74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE billing_adress_order ADD CONSTRAINT FK_48BC5B6B8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993984C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F5299398A76ED395 ON `order` (user_id)');
        $this->addSql('ALTER TABLE order_list ADD CONSTRAINT FK_939C20F4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_list ADD CONSTRAINT FK_939C20F8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD139DF194 FOREIGN KEY (promotion_id) REFERENCES promotion (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shipping_adress ADD CONSTRAINT FK_77B602F1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shipping_adress_order ADD CONSTRAINT FK_B2B8894B8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649FB8E54CD ON user');
        $this->addSql('ALTER TABLE user DROP wishlist_id');
        $this->addSql('DROP INDEX IDX_9CE12A314584665A ON wishlist');
        $this->addSql('ALTER TABLE wishlist ADD user_id INT NOT NULL, DROP product_id');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9CE12A31A76ED395 ON wishlist (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wishlist_product DROP FOREIGN KEY FK_4C46D2D7FB8E54CD');
        $this->addSql('ALTER TABLE wishlist_product DROP FOREIGN KEY FK_4C46D2D74584665A');
        $this->addSql('DROP TABLE wishlist_product');
        $this->addSql('ALTER TABLE billing_adress_order DROP FOREIGN KEY FK_48BC5B6B8D9F6D38');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1139DF194');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4584665A');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993984C3A3BB');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398A76ED395');
        $this->addSql('DROP INDEX IDX_F5299398A76ED395 ON `order`');
        $this->addSql('ALTER TABLE order_list DROP FOREIGN KEY FK_939C20F4584665A');
        $this->addSql('ALTER TABLE order_list DROP FOREIGN KEY FK_939C20F8D9F6D38');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD139DF194');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE shipping_adress DROP FOREIGN KEY FK_77B602F1A76ED395');
        $this->addSql('ALTER TABLE shipping_adress_order DROP FOREIGN KEY FK_B2B8894B8D9F6D38');
        $this->addSql('ALTER TABLE user ADD wishlist_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649FB8E54CD ON user (wishlist_id)');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31A76ED395');
        $this->addSql('DROP INDEX UNIQ_9CE12A31A76ED395 ON wishlist');
        $this->addSql('ALTER TABLE wishlist ADD product_id INT DEFAULT NULL, DROP user_id');
        $this->addSql('CREATE INDEX IDX_9CE12A314584665A ON wishlist (product_id)');
    }
}
