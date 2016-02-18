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
        'consys' AS `instance`
    FROM
        `cez_dbconsys`.`clients` `c` 
UNION ALL
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
        'art' AS `instance`
    FROM
        `cez_dbart`.`clients` `c` 
UNION ALL
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
        'edar' AS `instance`
    FROM
        `cez_dbedar`.`clients` `c` 
UNION ALL
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
        'eska' AS `instance`
    FROM
        `cez_dbeska`.`clients` `c` 
UNION ALL
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
        `cez_dbtest`.`clients` `c` 
UNION ALL
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
        'cnt1' AS `instance`
    FROM
        `cez_dbcnt1`.`clients` `c` 
UNION ALL
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
        'cnt2' AS `instance`
    FROM
        `cez_dbcnt2`.`clients` `c` ;