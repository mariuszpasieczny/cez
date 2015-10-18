ALTER TABLE servicereturns ADD COLUMN demagecodeid INT DEFAULT 0;
CREATE OR REPLACE
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
        `sr`.`waybill` AS `waybill`,
        `sr`.`demagecodeid` AS `demagecodeid`,
        `dc`.`acronym` AS `demagecodeacronym`,
        `dc`.`name` AS `demagecodename`
    FROM
        (((`servicereturns` `sr`
        JOIN `servicesview` `s` ON ((`s`.`id` = `sr`.`serviceid`)))
        LEFT JOIN `dictionaries` `d` ON ((`d`.`id` = `sr`.`statusid`)))
        LEFT JOIN `dictionaries` `dc` ON ((`dc`.`id` = `sr`.`demagecodeid`)))