


alter table products drop index serial;
insert into dictionaries (parentid,acronym,name) (select parentid,'new','Nowe' from dictionaries where acronym = 'instock');
ALTER TABLE products ADD pairedcard varchar(64);
CREATE 
VIEW `productsview` AS
    SELECT 
        `p`.`id` AS `id`,
        `p`.`warehouseid` AS `warehouseid`,
        `p`.`acronym` AS `acronym`,
        `p`.`name` AS `name`,
        `p`.`pairedcard` AS `pairedcard`,
        `p`.`description` AS `description`,
        `p`.`unitid` AS `unitid`,
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
        LEFT JOIN `dictionaries` `ds` ON ((`p`.`statusid` = `ds`.`id`)))










