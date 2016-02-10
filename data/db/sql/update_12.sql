CREATE OR REPLACE
VIEW `clientsview` AS
    SELECT 
        `c`.`id` AS `id`,
        `c`.`addressid` AS `addressid`,
        `c`.`city` AS `city`,
        `c`.`street` AS `street`,
        `c`.`number` AS `number`,
        `c`.`postcode` AS `postcode`,
        `c`.`dateadd` AS `dateadd`,
        `c`.`streetno` AS `streetno`,
        `c`.`apartmentno` AS `apartmentno`,
        `c`.`homephone` AS `homephone`,
        `c`.`workphone` AS `workphone`,
        `c`.`cellphone` AS `cellphone`,
        `c`.`statusid` AS `statusid`,
        'lublin' AS `instance`
    FROM
        `cez_dbtest`.`clients` `c` ;