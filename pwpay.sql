DROP DATABASE IF EXISTS `pwpay`
CREATE DATABASE IF NOT EXISTS `pwpay`;

DROP TABLE IF EXISTS `User`;
CREATE TABLE `User`(

	`user_id`		INT UNSIGNED	 	NOT NULL AUTO_INCREMENT,
	`name` 			VARCHAR(30) 		NOT NULL DEFAULT '',
	`lastname` 		VARCHAR(30) 		NOT NULL DEFAULT '',
	`email` 		VARCHAR(100) 		NOT NULL DEFAULT '',
	`password` 		VARCHAR(255) 		NOT NULL DEFAULT '',
	`birthdate` 	VARCHAR(40) 		NOT NULL,
	`phone` 		VARCHAR(20) 		NOT NULL DEFAULT '',
	`createdAt`		DATETIME			NOT NULL,
	`updatedAt` 	DATETIME			NOT NULL,
	`balance`		DECIMAL(18, 2)		NOT NULL DEFAULT '0.00',
	`iban` 			VARCHAR(255)		NOT NULL DEFAULT '',

	PRIMARY KEY (`user_id`)

)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Token`;
CREATE TABLE `Token`(

	`token` 		VARCHAR(20) 		NOT NULL,
	`user_id` 		INT UNSIGNED		NOT NULL,
	`activated` 	bit(1) 				DEFAULT b'0',

	FOREIGN KEY (`user_id`) REFERENCES User (`user_id`)

)ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Transactions`;
CREATE TABLE `Transactions`(
	`transaction_id` 	INT UNSIGNED 		NOT NULL AUTO_INCREMENT,
	`sender_id` 		INT UNSIGNED 		NOT NULL,
	`receiver_id` 		INT UNSIGNED		NOT NULL,
	`sender_name` 		VARCHAR(30) 		NOT NULL DEFAULT '',
	`receiver_name` 	VARCHAR(30) 		NOT NULL DEFAULT '',
	`amount` 			DECIMAL(18, 2) 		NOT NULL,
	`date` 				DATETIME 			NOT NULL,
	`payed` 			bit(1) 				DEFAULT b'0',
	
	PRIMARY KEY (`transaction_id`)

)ENGINE=InnoDB DEFAULT CHARSET=utf8;


