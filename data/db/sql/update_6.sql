ALTER TABLE servicereturns ADD COLUMN demagecodeid INT DEFAULT 0;
CREATE OR REPLACE
VIEW `servicereturnsview` AS
    SELECT 
        `sr`.`id` AS `id`,
        `sr`.`productid` AS `productid`,
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
        `sr`.`waybill` AS `waybill`,
        `sr`.`demagecodeid` AS `demagecodeid`,
        `dc`.`acronym` AS `demagecodeacronym`,
        `dc`.`name` AS `demagecodename`,
        `sr`.`catalogid` AS `catalogid`,
        `c`.`name` AS `catalogname`
    FROM
        ((((`servicereturns` `sr`
        JOIN `servicesview` `s` ON ((`s`.`id` = `sr`.`serviceid`)))
        LEFT JOIN `dictionaries` `d` ON ((`d`.`id` = `sr`.`statusid`)))
        LEFT JOIN `dictionaries` `dc` ON ((`dc`.`id` = `sr`.`demagecodeid`)))
        LEFT JOIN `catalog` `c` ON ((`c`.`id` = `sr`.`catalogid`))) 
    UNION SELECT 
        `sr`.`id` AS `id`,
        `sr`.`productid` AS `productid`,
        `sr`.`serviceid` AS `serviceid`,
        `sr`.`name` AS `name`,
        `sr`.`quantity` AS `quantity`,
        `sr`.`dateadd` AS `dateadd`,
        `sr`.`unitid` AS `unitid`,
        `sr`.`demaged` AS `demaged`,
        NULL AS `service`,
        `ol`.`technicianid` AS `technicianid`,
        NULL AS `clientnumber`,
        NULL AS `NULL`,
        CONCAT(`u`.`lastname`, ' ', `u`.`firstname`) AS `technician`,
        `sr`.`statusid` AS `statusid`,
        `d`.`name` AS `status`,
        `d`.`acronym` AS `statusacronym`,
        `sr`.`waybill` AS `waybill`,
        `sr`.`demagecodeid` AS `demagecodeid`,
        `dc`.`acronym` AS `demagecodeacronym`,
        `dc`.`name` AS `demagecodename`,
        `sr`.`catalogid` AS `catalogid`,
        `c`.`name` AS `catalogname`
    FROM
        (((((`servicereturns` `sr`
        JOIN `orderlines` `ol` ON ((`ol`.`id` = `sr`.`productid`)))
        LEFT JOIN `dictionaries` `d` ON ((`d`.`id` = `sr`.`statusid`)))
        LEFT JOIN `dictionaries` `dc` ON ((`dc`.`id` = `sr`.`demagecodeid`)))
        LEFT JOIN `catalog` `c` ON ((`c`.`id` = `sr`.`catalogid`)))
        LEFT JOIN `users` `u` ON ((`u`.`id` = `ol`.`technicianid`)));
-- __ID__ = select id from dictionaries where acronym = 'products';
insert into dictionaries (parentid,acronym,name) values (__ID__,'returned','Zwrócone');