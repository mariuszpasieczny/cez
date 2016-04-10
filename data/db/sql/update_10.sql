CREATE 
    OR REPLACE
VIEW `usersview` AS
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'consys' AS `region`
    FROM
        `cez_dbconsys`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'art' AS `region`
    FROM
        `cez_dbart`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'edar' AS `region`
    FROM
        `cez_dbedar`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'eska' AS `region`
    FROM
        `cez_dbeska`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'lublin' AS `region`
    FROM
        `cez_dblublin`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'cnt1' AS `region`
    FROM
        `cez_dbcnt1`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'cnt2' AS `region`
    FROM
        `cez_dbcnt2`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'wawrzyniak' AS `region`
    FROM
        `cez_dbcmt3`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'abmedia' AS `region`
    FROM
        `cez_dbgd1`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'tav' AS `region`
    FROM
        `cez_dbgd2`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'net2b' AS `region`
    FROM
        `cez_dbbd1`.`usersview`  
UNION ALL
    SELECT 
        `usersview`.`id` AS `id`,
        `usersview`.`symbol` AS `symbol`,
        `usersview`.`firstname` AS `firstname`,
        `usersview`.`lastname` AS `lastname`,
        `usersview`.`email` AS `email`,
        `usersview`.`phoneno` AS `phoneno`,
        `usersview`.`password` AS `password`,
        `usersview`.`role` AS `role`,
        `usersview`.`admin` AS `admin`,
        `usersview`.`dateadd` AS `dateadd`,
        `usersview`.`statusid` AS `statusid`,
        `usersview`.`status` AS `status`,
        'lucjad' AS `region`
    FROM
        `cez_dbbd2`.`usersview` ;