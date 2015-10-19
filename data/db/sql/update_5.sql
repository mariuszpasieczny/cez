CREATE TABLE `servicereturns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serviceid` int(11) DEFAULT NULL,
  `name` varchar(45) CHARACTER SET latin1 DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `unitid` int(11) DEFAULT NULL,
  `demaged` int(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productid` (`serviceid`,`productname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
CREATE 
    OR REPLACE
VIEW `servicereturnsview` AS
    SELECT 
        `sr`.`id` AS `id`,
        `sr`.`serviceid` AS `serviceid`,
        `sr`.`name` AS `name`,
        `sr`.`quantity` AS `quantity`,
        `sr`.`dateadd` AS `dateadd`,
        `sr`.`unitid` AS `unitid`,
        `sr`.`demaged` AS `demaged`,
        `s`.`number` AS `service`,
        `s`.`technicianid` AS `technicianid`,
        `s`.`clientnumber` AS `clientnumber`,
        `s`.`client` AS `client`
    FROM
        (`servicereturns` `sr`
        JOIN `servicesview` `s` ON ((`s`.`id` = `sr`.`serviceid`)));