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
        'lublin' AS `region`
    FROM
        `cez_dbtest`.`usersview` ;