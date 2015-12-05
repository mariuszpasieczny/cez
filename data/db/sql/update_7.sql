alter table products drop index serial;
ALTER TABLE products ADD pairedcard varchar(64);
ALTER TABLE products ADD price FLOAT(7,2);
ALTER TABLE orderlines ADD qtyavailable int(11) default 0;
update orderlines ol set qtyavailable = quantity where not exists (select 1 from serviceproducts sp where sp.productid = ol.id) and statusid = 22;
update orderlines ol set qtyavailable = 0 where  exists (select 1 from serviceproducts sp where sp.productid = ol.id) and statusid = 23 ;
ALTER TABLE serviceproducts ADD quantity int(11) NOT NULL;
ALTER TABLE orderlines ADD qtyreturned int(11) NOT NULL;
ALTER TABLE serviceproducts ADD dateadd TIMESTAMP NOT NULL default NOW();
ALTER TABLE servicereturns ADD productid int(11) NOT NULL;
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
        `p`.`price` AS `price`,
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
        `ol`.`qtyreturned` AS `qtyreturned`,
        `ol`.`qtyavailable` AS `qtyavailable`,
        `ol`.`qtyavailable` AS `qtyreleased`,
        `p`.`unitacronym` AS `unitacronym`,
        `p`.`unitid` AS `unitid`,
        `ol`.`dateadd` AS `dateadd`,
        `ol`.`releasedate` AS `releasedate`,
        `sp`.`serviceid` AS `serviceid`,
        (SELECT 
                `d`.`id`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'new'))) AS `statusid`,
        CONCAT(`u`.`lastname`, ' ', `u`.`firstname`) AS `technician`,
        (SELECT 
                `d`.`name`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'new'))) AS `status`,
        (SELECT 
                `d`.`acronym`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'new'))) AS `statusacronym`,
        `p`.`name` AS `product`,
        `p`.`serial` AS `serial`,
        NULL AS `client`,
        NULL AS `clientid`,
        NULL AS `clientnumber`,
        `o`.`userid` AS `userid`
    FROM
        (((((((`orderlines` `ol`
        LEFT JOIN `users` `u` ON ((`ol`.`technicianid` = `u`.`id`)))
        LEFT JOIN `dictionaries` `ds` ON ((`ol`.`statusid` = `ds`.`id`)))
        LEFT JOIN `productsview` `p` ON ((`ol`.`productid` = `p`.`id`)))
        LEFT JOIN `serviceproducts` `sp` ON ((`sp`.`productid` = `ol`.`id`)))
        LEFT JOIN `services` `s` ON ((`sp`.`serviceid` = `s`.`id`)))
        LEFT JOIN `orders` `o` ON ((`ol`.`orderid` = `o`.`id`)))
        LEFT JOIN `clients` `c` ON ((`c`.`id` = `s`.`clientid`)))
    WHERE
        ((`ol`.`qtyavailable` = 0)
            AND (`ol`.`qtyreturned` = 0)
            AND (`ol`.`statusid` = (SELECT 
                `d`.`id`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'new'))))) 
    UNION SELECT 
        `p`.`warehouseid` AS `warehouseid`,
        `p`.`warehouse` AS `warehouse`,
        `ol`.`id` AS `id`,
        `ol`.`orderid` AS `orderid`,
        `ol`.`productid` AS `productid`,
        `ol`.`technicianid` AS `technicianid`,
        `ol`.`quantity` AS `quantity`,
        `ol`.`qtyreturned` AS `qtyreturned`,
        `ol`.`qtyavailable` AS `qtyavailable`,
        `ol`.`qtyavailable` AS `qtyreleased`,
        `p`.`unitacronym` AS `unitacronym`,
        `p`.`unitid` AS `unitid`,
        `ol`.`dateadd` AS `dateadd`,
        `ol`.`releasedate` AS `releasedate`,
        `sp`.`serviceid` AS `serviceid`,
        (SELECT 
                `d`.`id`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'released'))) AS `statusid`,
        CONCAT(`u`.`lastname`, ' ', `u`.`firstname`) AS `technician`,
        (SELECT 
                `d`.`name`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'released'))) AS `status`,
        (SELECT 
                `d`.`acronym`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'released'))) AS `statusacronym`,
        `p`.`name` AS `product`,
        `p`.`serial` AS `serial`,
        NULL AS `client`,
        NULL AS `clientid`,
        NULL AS `clientnumber`,
        `o`.`userid` AS `userid`
    FROM
        (((((((`orderlines` `ol`
        LEFT JOIN `users` `u` ON ((`ol`.`technicianid` = `u`.`id`)))
        LEFT JOIN `dictionaries` `ds` ON ((`ol`.`statusid` = `ds`.`id`)))
        LEFT JOIN `productsview` `p` ON ((`ol`.`productid` = `p`.`id`)))
        LEFT JOIN `serviceproducts` `sp` ON ((`sp`.`productid` = `ol`.`id`)))
        LEFT JOIN `services` `s` ON ((`sp`.`serviceid` = `s`.`id`)))
        LEFT JOIN `orders` `o` ON ((`ol`.`orderid` = `o`.`id`)))
        LEFT JOIN `clients` `c` ON ((`c`.`id` = `s`.`clientid`)))
    WHERE
        ((`ol`.`qtyavailable` > 0)
            AND (`ol`.`technicianid` > 0)) 
    UNION SELECT 
        `p`.`warehouseid` AS `warehouseid`,
        `p`.`warehouse` AS `warehouse`,
        `ol`.`id` AS `id`,
        `ol`.`orderid` AS `orderid`,
        `ol`.`productid` AS `productid`,
        `ol`.`technicianid` AS `technicianid`,
        `ol`.`quantity` AS `quantity`,
        `ol`.`qtyreturned` AS `qtyreturned`,
        `ol`.`qtyavailable` AS `qtyavailable`,
        `sp`.`quantity` AS `qtyreleased`,
        `p`.`unitacronym` AS `unitacronym`,
        `p`.`unitid` AS `unitid`,
        `ol`.`dateadd` AS `dateadd`,
        `ol`.`releasedate` AS `releasedate`,
        `sp`.`serviceid` AS `serviceid`,
        (SELECT 
                `d`.`id`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'invoiced'))) AS `statusid`,
        CONCAT(`u`.`lastname`, ' ', `u`.`firstname`) AS `technician`,
        (SELECT 
                `d`.`name`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'invoiced'))) AS `status`,
        (SELECT 
                `d`.`acronym`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'invoiced'))) AS `statusacronym`,
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
        JOIN `serviceproducts` `sp` ON ((`sp`.`productid` = `ol`.`id`)))
        LEFT JOIN `services` `s` ON ((`sp`.`serviceid` = `s`.`id`)))
        LEFT JOIN `orders` `o` ON ((`ol`.`orderid` = `o`.`id`)))
        LEFT JOIN `clients` `c` ON ((`c`.`id` = `s`.`clientid`))) 
    UNION SELECT 
        `p`.`warehouseid` AS `warehouseid`,
        `p`.`warehouse` AS `warehouse`,
        `ol`.`id` AS `id`,
        `ol`.`orderid` AS `orderid`,
        `ol`.`productid` AS `productid`,
        `ol`.`technicianid` AS `technicianid`,
        `ol`.`quantity` AS `quantity`,
        `ol`.`qtyreturned` AS `qtyreturned`,
        `ol`.`qtyavailable` AS `qtyavailable`,
        `ol`.`qtyreturned` AS `qtyreleased`,
        `p`.`unitacronym` AS `unitacronym`,
        `p`.`unitid` AS `unitid`,
        `ol`.`dateadd` AS `dateadd`,
        `ol`.`releasedate` AS `releasedate`,
        NULL AS `serviceid`,
        (SELECT 
                `d`.`id`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'returned'))) AS `statusid`,
        CONCAT(`u`.`lastname`, ' ', `u`.`firstname`) AS `technician`,
        (SELECT 
                `d`.`name`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'returned'))) AS `status`,
        (SELECT 
                `d`.`acronym`
            FROM
                (`dictionaries` `p`
                JOIN `dictionaries` `d` ON ((`d`.`parentid` = `p`.`id`)))
            WHERE
                ((`p`.`acronym` = 'orders')
                    AND (`d`.`acronym` = 'returned'))) AS `statusacronym`,
        `p`.`name` AS `product`,
        `p`.`serial` AS `serial`,
        NULL AS `client`,
        NULL AS `clientid`,
        NULL AS `clientnumber`,
        `o`.`userid` AS `userid`
    FROM
        (((((`orderlines` `ol`
        LEFT JOIN `users` `u` ON ((`ol`.`technicianid` = `u`.`id`)))
        LEFT JOIN `dictionaries` `ds` ON ((`ol`.`statusid` = `ds`.`id`)))
        LEFT JOIN `productsview` `p` ON ((`ol`.`productid` = `p`.`id`)))
        LEFT JOIN `servicereturns` `sr` ON ((`sr`.`productid` = `ol`.`id`)))
        LEFT JOIN `orders` `o` ON ((`ol`.`orderid` = `o`.`id`)))
    WHERE
        (`ol`.`qtyreturned` > 0);

update serviceproducts sp set quantity = (select quantity - qtyavailable from orderlines ol where ol.id = sp.productid);
insert into dictionaries (parentid,acronym,name) (select parentid,'new','Nowe' from dictionaries where acronym = 'instock');
CREATE  OR REPLACE
VIEW `serviceproductsview` AS
    SELECT 
        `sp`.`id` AS `id`,
        `sp`.`serviceid` AS `serviceid`,
        `sp`.`productid` AS `productid`,
        `sp`.`quantity` AS `quantity`,
        IF(ISNULL(`p`.`name`),
            `sp`.`productname`,
            `p`.`name`) AS `name`,
        `p`.`serial` AS `serial`,
        `p`.`unitid` AS `unitid`
    FROM
        ((`serviceproducts` `sp`
        LEFT JOIN `orderlines` `ol` ON ((`sp`.`productid` = `ol`.`id`)))
        LEFT JOIN `products` `p` ON ((`p`.`id` = `ol`.`productid`)));