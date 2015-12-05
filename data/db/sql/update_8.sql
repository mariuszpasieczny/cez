CREATE TABLE IF NOT EXISTS `catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identyfikator',
  `name` varchar(255) NOT NULL COMMENT 'nazwa',
  `statusid` int(11) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  UNIQUE KEY `productid` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
CREATE OR REPLACE 
VIEW `catalogview` AS
    SELECT 
        `c`.`id` AS `id`,
        `c`.`name` AS `name`,
        `c`.`statusid` AS `statusid`,
        `d`.`name` AS `status`
    FROM
        (`catalog` `c`
        LEFT JOIN `dictionaries` `d` ON ((`c`.`statusid` = `d`.`id`))) 
;
insert into dictionaries (parentid,acronym,name) values (4,'catalog','Statusy katalogu produktów');
-- __ID__ = select max(id) from dictionaries;
insert into dictionaries (parentid,acronym,name) values (__ID__,'active','Aktywny');
insert into dictionaries (parentid,acronym,name) values (__ID__,'inactive','Nieaktywny');
insert into dictionaries (parentid,acronym,name) values (__ID__,'deleted','Usunięty');
ALTER TABLE servicereturns ADD catalogid int(11) NOT NULL;