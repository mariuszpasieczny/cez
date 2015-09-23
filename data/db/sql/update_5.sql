CREATE TABLE `servicereturns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serviceid` int(11) DEFAULT NULL,
  `name` varchar(45) CHARACTER SET latin1 DEFAULT NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unitid` int(11) DEFAULT NULL,
  `demaged` int(1) DEFAULT 0,
  `statusid` int(11) DEFAULT 0,
  `waybill` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `productid` (`serviceid`,`name`)
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
        `s`.`client` AS `client`,
        `s`.`technician` AS `technician`,
        `sr`.`statusid` AS `statusid`,
        `d`.`name` AS `status`,
        `d`.`acronym` AS `statusacronym`,
        `sr`.`waybill`
    FROM
        ((`servicereturns` `sr`
        JOIN `servicesview` `s` ON ((`s`.`id` = `sr`.`serviceid`)))
        LEFT JOIN `dictionaries` `d` ON ((`d`.`id` = `sr`.`statusid`)));
insert into dictionaries (parentid,acronym,name) values (4,'returns','Statusy zwrotów');
insert into dictionaries (parentid,acronym,name) values (2581,'new','Nowe');
insert into dictionaries (parentid,acronym,name) values (2581,'accepted','Przyjęte');
insert into dictionaries (parentid,acronym,name) values (2581,'sent','Wysłane');