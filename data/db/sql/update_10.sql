ALTER TABLE services MODIFY COLUMN comments TEXT;
ALTER TABLE services MODIFY COLUMN slots TEXT;
ALTER TABLE services MODIFY COLUMN products TEXT;
ALTER TABLE services MODIFY COLUMN productsreturned TEXT;
ALTER TABLE services MODIFY COLUMN serialnumbers TEXT;
ALTER TABLE services MODIFY COLUMN macnumbers TEXT;
ALTER TABLE services MODIFY COLUMN technicalcomments TEXT;
ALTER TABLE services MODIFY COLUMN coordinatorcomments TEXT;
CREATE OR REPLACE
VIEW `servicesview` AS
    SELECT 
        `s`.`id` AS `id`,
        `s`.`number` AS `number`,
        `s`.`warehouseid` AS `warehouseid`,
        `s`.`description` AS `description`,
        `s`.`parameters` AS `parameters`,
        `s`.`modifieddate` AS `modifieddate`,
        `s`.`equipment` AS `equipment`,
        `s`.`servicetypeid` AS `servicetypeid`,
        `s`.`typeid` AS `typeid`,
        `s`.`clientid` AS `clientid`,
        `s`.`addressid` AS `addressid`,
        `s`.`dateadd` AS `dateadd`,
        `s`.`statusid` AS `statusid`,
        `s`.`planneddate` AS `planneddate`,
        `s`.`technicianid` AS `technicianid`,
        `s`.`timefrom` AS `timefrom`,
        `s`.`timetill` AS `timetill`,
        `s`.`regionid` AS `regionid`,
        `s`.`blockadecodeid` AS `blockadecodeid`,
        `s`.`laborcodeid` AS `laborcodeid`,
        `s`.`complaintcodeid` AS `complaintcodeid`,
        `s`.`calendarid` AS `calendarid`,
        `s`.`comments` AS `comments`,
        `s`.`areaid` AS `areaid`,
        `s`.`slots` AS `slots`,
        `s`.`products` AS `products`,
        `s`.`productsreturned` AS `productsreturned`,
        `s`.`serialnumbers` AS `serialnumbers`,
        `s`.`macnumbers` AS `macnumbers`,
        `s`.`technicalcomments` AS `technicalcomments`,
        `s`.`technicalcommentsrequired` AS `technicalcommentsrequired`,
        `s`.`coordinatorcomments` AS `coordinatorcomments`,
        `s`.`datefinished` AS `datefinished`,
        `s`.`performed` AS `performed`,
        `s`.`documentspassed` AS `documentspassed`,
        `s`.`closedupc` AS `closedupc`,
        CONCAT(`c`.`street`,
                CAST(' ' AS CHAR CHARSET UTF8),
                CAST(`c`.`streetno` AS CHAR CHARSET UTF8),
                CAST('/' AS CHAR CHARSET UTF8),
                CAST(IFNULL(`c`.`apartmentno`, '') AS CHAR CHARSET UTF8),
                CAST(', ' AS CHAR CHARSET UTF8),
                `c`.`city`) AS `client`,
        `c`.`number` AS `clientnumber`,
        `c`.`city` AS `clientcity`,
        `c`.`street` AS `clientstreet`,
        `c`.`streetno` AS `clientstreetno`,
        `c`.`apartmentno` AS `clientapartment`,
        `c`.`postcode` AS `clientpostcode`,
        `c`.`homephone` AS `clienthomephone`,
        `c`.`workphone` AS `clientworkphone`,
        `c`.`cellphone` AS `clientcellphone`,
        CONCAT(`u`.`lastname`, ' ', `u`.`firstname`) AS `technician`,
        `ds`.`name` AS `status`,
        `ds`.`acronym` AS `statusacronym`,
        `dt`.`acronym` AS `servicetype`,
        `cal`.`acronym` AS `calendar`,
        `ar`.`acronym` AS `area`,
        `cb`.`acronym` AS `blockadecode`,
        `cl`.`acronym` AS `laborcode`,
        `cc2`.`acronym` AS `complaintcode`,
        `dr`.`acronym` AS `region`,
        `s`.`systemid` AS `systemid`,
        `sys`.`acronym` AS `system`,
        `d`.`name` AS `type`
    FROM
        (((((((((((((`services` `s`
        LEFT JOIN `clients` `c` ON ((`s`.`clientid` = `c`.`id`)))
        LEFT JOIN `users` `u` ON ((`s`.`technicianid` = `u`.`id`)))
        LEFT JOIN `dictionaries` `ds` ON ((`s`.`statusid` = `ds`.`id`)))
        LEFT JOIN `dictionaries` `dt` ON ((`s`.`servicetypeid` = `dt`.`id`)))
        LEFT JOIN `dictionaries` `cal` ON ((`s`.`calendarid` = `cal`.`id`)))
        LEFT JOIN `dictionaries` `ar` ON ((`s`.`areaid` = `ar`.`id`)))
        LEFT JOIN `dictionaries` `r` ON ((`s`.`regionid` = `r`.`id`)))
        LEFT JOIN `dictionaries` `cb` ON ((`s`.`blockadecodeid` = `cb`.`id`)))
        LEFT JOIN `dictionaries` `cl` ON ((`s`.`laborcodeid` = `cl`.`id`)))
        LEFT JOIN `dictionaries` `cc2` ON ((`s`.`complaintcodeid` = `cc2`.`id`)))
        LEFT JOIN `dictionaries` `dr` ON ((`s`.`regionid` = `dr`.`id`)))
        LEFT JOIN `dictionaries` `sys` ON ((`s`.`systemid` = `sys`.`id`)))
        LEFT JOIN `dictionaries` `d` ON ((`s`.`typeid` = `d`.`id`)));
