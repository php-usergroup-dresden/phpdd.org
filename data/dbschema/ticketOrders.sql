USE `phpdd18`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `ticketOrders`;
CREATE TABLE `ticketOrders` (
  `id`                int(10) unsigned NOT NULL AUTO_INCREMENT
  COMMENT 'Record-ID',
  `orderId`           VARCHAR(100)     NOT NULL
  COMMENT 'Ticket order ID',
  `date`              DATETIME         NOT NULL
  COMMENT 'Order date',
  `email`             VARCHAR(255)     NOT NULL
  COMMENT 'Email address',
  `paymentProvider`   VARCHAR(100)     NOT NULL
  COMMENT 'Payment provider',
  `currencyCode`      CHAR(3)          NOT NULL DEFAULT 'EUR'
  COMMENT 'Currency code',
  `orderTotal`        INT(10) UNSIGNED NOT NULL
  COMMENT 'Order total',
  `discountTotal`     INT(11)          NOT NULL
  COMMENT 'Discount total',
  `diversityDonation` INT UNSIGNED     NOT NULL
  COMMENT 'Diversity donation',
  `paymentFee`        INT UNSIGNED     NOT NULL
  COMMENT 'Payment fee',
  `paymentTotal`      INT UNSIGNED     NOT NULL
  COMMENT 'Payment total',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderId` (`orderId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT ='Ticket orders'
  AUTO_INCREMENT = 1;

DROP TABLE IF EXISTS `ticketOrderAddresses`;
CREATE TABLE IF NOT EXISTS `ticketOrderAddresses` (
  `id`               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Record-ID',
  `orderId`          VARCHAR(100)     NOT NULL
  COMMENT 'Ticket order ID',
  `companyName`      VARCHAR(100)              DEFAULT NULL
  COMMENT 'Company name',
  `firstname`        VARCHAR(100)     NOT NULL
  COMMENT 'Firstname',
  `lastname`         VARCHAR(100)     NOT NULL
  COMMENT 'Lastname',
  `streetWithNumber` VARCHAR(100)     NOT NULL
  COMMENT 'Street with number',
  `addressAddon`     VARCHAR(100)              DEFAULT NULL
  COMMENT 'Address addon',
  `zipCode`          VARCHAR(50)      NOT NULL
  COMMENT 'Zip code',
  `city`             VARCHAR(100)     NOT NULL
  COMMENT 'city',
  `countryCode`      CHAR(2)          NOT NULL
  COMMENT 'Country code',
  `vatNumber`        VARCHAR(100)              DEFAULT NULL
  COMMENT 'VAT number',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderId` (`orderId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT ='Ticket order addresses'
  AUTO_INCREMENT = 1;

ALTER TABLE `ticketOrderAddresses`
  ADD CONSTRAINT `orderAddressRelation` FOREIGN KEY (`orderId`) REFERENCES `ticketOrders` (`orderId`);

DROP TABLE IF EXISTS `ticketOrderItems`;
CREATE TABLE IF NOT EXISTS `ticketOrderItems` (
  `id`           INT(10) UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Record-ID',
  `itemId`       VARCHAR(100)     NOT NULL
  COMMENT 'Ticket item ID',
  `orderId`      VARCHAR(100)     NOT NULL
  COMMENT 'Ticket order ID',
  `ticketId`     VARCHAR(50)      NOT NULL
  COMMENT 'Ticket ID',
  `attendeeName` VARCHAR(255)     NOT NULL
  COMMENT 'Attendee name',
  `discountCode` VARCHAR(50)               DEFAULT NULL
  COMMENT 'Discount code',
  PRIMARY KEY (`id`),
  INDEX `orderId` (`orderId`),
  UNIQUE KEY `itemId` (`itemId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT ='Ticket order items'
  AUTO_INCREMENT = 1;

ALTER TABLE `ticketOrderItems`
  ADD CONSTRAINT `orderItemsRelation` FOREIGN KEY (`orderId`) REFERENCES `ticketOrders` (`orderId`);

DROP TABLE IF EXISTS `ticketOrderPayments`;
CREATE TABLE IF NOT EXISTS `ticketOrderPayments` (
  `id`         INT(10) UNSIGNED             NOT NULL AUTO_INCREMENT
  COMMENT 'Record-ID',
  `paymentId`  VARCHAR(255)                 NOT NULL
  COMMENT 'Ticket payment ID',
  `orderId`    VARCHAR(100)                 NOT NULL
  COMMENT 'Ticket order ID',
  `payerId`    VARCHAR(255)                 NOT NULL
  COMMENT 'Payer ID',
  `metaData`   TEXT                         NOT NULL
  COMMENT 'Meta data',
  `status`     ENUM ('pending', 'executed') NOT NULL DEFAULT 'pending'
  COMMENT 'Payment status',
  `executedAt` DATETIME                              DEFAULT NULL
  COMMENT 'Date payment was executed',
  PRIMARY KEY (`id`),
  UNIQUE KEY `paymentId` (`paymentId`),
  INDEX `orderId` (`orderId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT ='Ticket order payments'
  AUTO_INCREMENT = 1;

ALTER TABLE `ticketOrderPayments`
  ADD CONSTRAINT `orderPaymentsRelation` FOREIGN KEY (`orderId`) REFERENCES `ticketOrders` (`orderId`);

DROP TABLE IF EXISTS `ticketOrderMails`;
CREATE TABLE IF NOT EXISTS `ticketOrderMails` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Record-ID',
  `orderId` VARCHAR(100)     NOT NULL
  COMMENT 'Ticket order ID',
  `sentAt`  DATETIME         NOT NULL
  COMMENT 'Date email was sent',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderId` (`orderId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT ='Ticket order payments'
  AUTO_INCREMENT = 1;

ALTER TABLE `ticketOrderMails`
  ADD CONSTRAINT `orderMailsRelation` FOREIGN KEY (`orderId`) REFERENCES `ticketOrders` (`orderId`);

DROP TABLE IF EXISTS `ticketOrderInvoices`;
CREATE TABLE IF NOT EXISTS `ticketOrderInvoices` (
  `id`        INT(10) UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Record-ID',
  `orderId`   VARCHAR(100)     NOT NULL
  COMMENT 'Ticket order ID',
  `invoiceId` VARCHAR(50)      NOT NULL
  COMMENT 'Invoice ID',
  `date`      DATETIME         NOT NULL
  COMMENT 'Date invoice was created',
  `pdf` LONGBLOB NOT NULL
  COMMENT 'PDF invoice',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderId` (`orderId`),
  UNIQUE KEY `invoiceId` (`invoiceId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT ='Ticket order invoices'
  AUTO_INCREMENT = 1;

ALTER TABLE `ticketOrderInvoices`
  ADD CONSTRAINT `orderInvoicesRelation` FOREIGN KEY (`orderId`) REFERENCES `ticketOrders` (`orderId`);

DROP TABLE IF EXISTS `ticketOrderInvoiceSequence`;
CREATE TABLE IF NOT EXISTS `ticketOrderInvoiceSequence` (
  `sequence` INT(10) UNSIGNED NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COMMENT ='Ticket order invoice sequence';

INSERT INTO `ticketOrderInvoiceSequence` (sequence) VALUES (1);

SET FOREIGN_KEY_CHECKS = 1;