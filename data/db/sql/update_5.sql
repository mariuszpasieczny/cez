ALTER TABLE orderlines ADD qtyavailable int(11) default 0;
update orderlines set qtyavailable = quantity where serviceid is null or serviceid = 0;
ALTER TABLE orderlines DROP serviceid;
CREATE OR REPLACE
VIEW `productsview` AS
    SELECT 
        `p`.`id` AS `id`,
        `p`.`warehouseid` AS `warehouseid`,
        `p`.`acronym` AS `acronym`,
        `p`.`name` AS `name`,
        `p`.`pairedcard` AS `pairedcard`,
        `p`.`description` AS `description`,
        `p`.`unitid` AS `unitid`,
        `d`.`acronym` AS `unitacronym`,
        `p`.`quantity` AS `quantity`,
        `p`.`qtyreserved` AS `qtyreserved`,
        `p`.`qtyreleased` AS `qtyreleased`,
        `p`.`qtyreturned` AS `qtyreturned`,
        `p`.`qtyavailable` AS `qtyavailable`,
        `p`.`serial` AS `serial`,
        `p`.`dateadd` AS `dateadd`,
        `p`.`statusid` AS `statusid`,
        `w`.`name` AS `warehouse`,
        `d`.`name` AS `unit`,
        `ds`.`name` AS `status`,
        `ds`.`acronym` AS `statusacronym`
    FROM
        (((`products` `p`
        LEFT JOIN `warehouses` `w` ON ((`p`.`warehouseid` = `w`.`id`)))
        LEFT JOIN `dictionaries` `d` ON ((`p`.`unitid` = `d`.`id`)))
        LEFT JOIN `dictionaries` `ds` ON ((`p`.`statusid` = `ds`.`id`)));
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
        `ol`.`qtyavailable` AS `qtyavailable`,
        `p`.`unitacronym`,
        `p`.`unitid` AS `unitid`,
        `ol`.`dateadd` AS `dateadd`,
        `ol`.`releasedate` AS `releasedate`,
        `sp`.`serviceid` AS `serviceid`,
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
        (((((((`orderlines` `ol`
        LEFT JOIN `users` `u` ON ((`ol`.`technicianid` = `u`.`id`)))
        LEFT JOIN `dictionaries` `ds` ON ((`ol`.`statusid` = `ds`.`id`)))
        LEFT JOIN `productsview` `p` ON ((`ol`.`productid` = `p`.`id`)))
        LEFT JOIN `serviceproducts` `sp` ON ((`sp`.`productid` = `ol`.`id`)))
        LEFT JOIN `services` `s` ON ((`sp`.`serviceid` = `s`.`id`)))
        LEFT JOIN `orders` `o` ON ((`ol`.`orderid` = `o`.`id`)))
        LEFT JOIN `clients` `c` ON ((`c`.`id` = `s`.`clientid`)));
ALTER TABLE serviceproducts ADD quantity int(11) NOT NULL;
update serviceproducts sp set quantity = (select quantity - qtyavailable from orderlines ol where ol.id = sp.productid);
CREATE OR REPLACE
VIEW `serviceproductsview` AS
    SELECT 
        `sp`.`id` AS `id`,
        `sp`.`serviceid` AS `serviceid`,
        `sp`.`productid` AS `productid`,
        `sp`.`quantity` AS `quantity`,
        IF(ISNULL(`p`.`product`),
            `sp`.`productname`,
            `p`.`product`) AS `name`,
        `p`.`serial` AS `serial`,
        `p`.`unitid` AS `unitid`
    FROM
        (`serviceproducts` `sp`
        LEFT JOIN `orderlinesview` `p` ON ((`sp`.`productid` = `p`.`id`)));