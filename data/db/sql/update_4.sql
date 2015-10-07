CREATE 
    OR REPLACE
VIEW `servicecodesview` AS
    SELECT 
        `sc`.`id` AS `id`,
        `sc`.`serviceid` AS `serviceid`,
        `s`.`technicianid` AS `technicianid`,
        `s`.`technician` AS `technician`,
        `s`.`statusacronym` AS `statusacronym`,
        `s`.`status` AS `status`,
        `s`.`statusid` AS `statusid`,
        `s`.`datefinished` AS `datefinished`,
        `s`.`planneddate` AS `planneddate`,
        `sc`.`attributeid` AS `attributeid`,
        `sc`.`codeid` AS `codeid`,
        `a`.`acronym` AS `attributeacronym`,
        `a`.`name` AS `attributename`,
        `d`.`acronym` AS `codeacronym`,
        `d`.`name` AS `codename`
    FROM
        (((`servicecodes` `sc`
        JOIN `servicesview` `s` ON ((`s`.`id` = `sc`.`serviceid`)))
        LEFT JOIN `dictionaries` `d` ON ((`sc`.`codeid` = `d`.`id`)))
        LEFT JOIN `dictionaries` `a` ON ((`sc`.`attributeid` = `a`.`id`)));
CREATE 
    OR REPLACE
VIEW `servicecodesstatistics` AS
    SELECT 
        `servicecodesview`.`technicianid` AS `technicianid`,
        IF(ISNULL(`servicecodesview`.`technicianid`),
            'nieprzypisane',
            `servicecodesview`.`technician`) AS `technician`,
        `servicecodesview`.`attributeacronym` AS `attributeacronym`,
        `servicecodesview`.`codeacronym` AS `codeacronym`,
        COUNT(`servicecodesview`.`id`) AS `quantity`
    FROM
        `servicecodesview`
    GROUP BY `servicecodesview`.`technicianid` , `servicecodesview`.`technician` , `servicecodesview`.`attributeacronym` , `servicecodesview`.`codeacronym`;
