DROP TABLE IF EXISTS `#__jshopping_discounts`;

CREATE TABLE `#__jshopping_discounts` (
`discount_id` int(11) NOT NULL AUTO_INCREMENT,
`product_id` int(11) NOT NULL,
`discount_type` int(11) NOT NULL DEFAULT 0,
`discount_value` DECIMAL(32) NOT NULL DEFAULT 0.0000,
`discount_apply` int(11) NOT NULL DEFAULT 0,
`start_date` datetime NOT NULL DEFAULT '0000-00-00',
`expire_date` datetime NOT NULL DEFAULT '0000-00-00',
UNIQUE (`product_id`),
PRIMARY KEY  (`discount_id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
