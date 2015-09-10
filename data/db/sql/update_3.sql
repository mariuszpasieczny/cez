CREATE OR REPLACE
VIEW `orderlinesview` AS
    SELECT 
        `p`.`warehouseid` AS `warehouseid`,
        `p`.`warehouse` AS `warehouse`,
        `ol`.`id` AS `id`,
        `ol`.`orderid` AS `orderid`,
        `ol`.`productid` AS `productid`,
        `ol`.`technicianid` AS `technicianid`,
        `ol`.`quantity` AS `quantity`,
        `ol`.`dateadd` AS `dateadd`,
        `ol`.`releasedate` AS `releasedate`,
        `ol`.`serviceid` AS `serviceid`,
        `ol`.`statusid` AS `statusid`,
        CONCAT(`u`.`lastname`, ' ', `u`.`firstname`) AS `technician`,
        `ds`.`name` AS `status`,
        `ds`.`acronym` AS `statusacronym`,
        `p`.`name` AS `product`,
        `p`.`serial` AS `serial`,
        CONCAT(`c`.`street`,
                CAST(' ' AS CHAR CHARSET UTF8),
                CAST(`c`.`streetno` AS CHAR CHARSET UTF8),
                CAST('/' AS CHAR CHARSET UTF8),
                CAST(IFNULL(`c`.`apartmentno`, '') AS CHAR CHARSET UTF8),
                CAST(', ' AS CHAR CHARSET UTF8),
                `c`.`city`) AS `client`,
        `s`.`clientid` AS `clientid`,
        `c`.`number` AS `clientnumber`,
        `o`.`userid` AS `userid`
    FROM
        ((((((`orderlines` `ol`
        LEFT JOIN `users` `u` ON ((`ol`.`technicianid` = `u`.`id`)))
        LEFT JOIN `dictionaries` `ds` ON ((`ol`.`statusid` = `ds`.`id`)))
        LEFT JOIN `productsview` `p` ON ((`ol`.`productid` = `p`.`id`)))
        LEFT JOIN `services` `s` ON ((`ol`.`serviceid` = `s`.`id`)))
        LEFT JOIN `orders` `o` ON ((`ol`.`orderid` = `o`.`id`)))
        LEFT JOIN `clients` `c` ON ((`c`.`id` = `s`.`clientid`)))
