-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 28 Cze 2015, 16:17
-- Wersja serwera: 5.5.21-log
-- Wersja PHP: 5.3.20

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `esoz`
--

-- --------------------------------------------------------

--
-- Struktura widoku `dictionaryattributesview`
--
DROP TABLE IF EXISTS `dictionaryattributesview`;

CREATE OR REPLACE VIEW `dictionaryattributesview` AS select `da`.`id` AS `id`,`da`.`entryid` AS `entryid`,`da`.`attributeid` AS `attributeid`,`da`.`value` AS `value`,`d`.`acronym` AS `attributeacronym`,`dv`.`acronym` AS `attributecode`,`dv`.`name` AS `attributevalue` from ((`dictionaryattributes` `da` left join `dictionaries` `d` on((`da`.`attributeid` = `d`.`id`))) left join `dictionaries` `dv` on((`dv`.`id` = `da`.`value`)));

-- --------------------------------------------------------

--
-- Struktura widoku `dictionariesview`
--
DROP TABLE IF EXISTS `dictionariesview`;

CREATE OR REPLACE VIEW `dictionariesview` AS select `d`.`id` AS `id`,`d`.`parentid` AS `parentid`,`d`.`acronym` AS `acronym`,`d`.`name` AS `name`,`d`.`dateadd` AS `dateadd`,`d`.`statusid` AS `statusid`,(select `da`.`value` from `dictionaryattributesview` `da` where ((`da`.`entryid` = `d`.`id`) and (`da`.`attributeacronym` = 'errorcodeid'))) AS `errorcodeid`,(select `da`.`attributevalue` from `dictionaryattributesview` `da` where ((`da`.`entryid` = `d`.`id`) and (`da`.`attributeacronym` = 'errorcodeid'))) AS `errorcodename`,(select `da`.`attributecode` from `dictionaryattributesview` `da` where ((`da`.`entryid` = `d`.`id`) and (`da`.`attributeacronym` = 'errorcodeid'))) AS `errorcodeacronym`,(select `da`.`value` from `dictionaryattributesview` `da` where ((`da`.`entryid` = `d`.`id`) and (`da`.`attributeacronym` = 'price'))) AS `price`,(select `da`.`value` from `dictionaryattributesview` `da` where ((`da`.`entryid` = `d`.`id`) and (`da`.`attributeacronym` = 'datefrom'))) AS `datefrom`,(select `da`.`value` from `dictionaryattributesview` `da` where ((`da`.`entryid` = `d`.`id`) and (`da`.`attributeacronym` = 'datetill'))) AS `datetill` from `dictionaries` `d`;

-- --------------------------------------------------------

--
-- Struktura widoku `productsview`
--
DROP TABLE IF EXISTS `productsview`;

CREATE OR REPLACE VIEW `productsview` AS select `p`.`id` AS `id`,`p`.`warehouseid` AS `warehouseid`,`p`.`acronym` AS `acronym`,`p`.`name` AS `name`,`p`.`description` AS `description`,`p`.`unitid` AS `unitid`,`p`.`quantity` AS `quantity`,`p`.`qtyreserved` AS `qtyreserved`,`p`.`qtyreleased` AS `qtyreleased`,`p`.`qtyreturned` AS `qtyreturned`,`p`.`qtyavailable` AS `qtyavailable`,`p`.`serial` AS `serial`,`p`.`dateadd` AS `dateadd`,`p`.`statusid` AS `statusid`,`w`.`name` AS `warehouse`,`d`.`name` AS `unit`,`ds`.`name` AS `status`,`ds`.`acronym` AS `statusacronym` from (((`products` `p` left join `warehouses` `w` on((`p`.`warehouseid` = `w`.`id`))) left join `dictionaries` `d` on((`p`.`unitid` = `d`.`id`))) left join `dictionaries` `ds` on((`p`.`statusid` = `ds`.`id`)));

-- --------------------------------------------------------

--
-- Struktura widoku `servicecodesview`
--
DROP TABLE IF EXISTS `servicecodesview`;

CREATE OR REPLACE VIEW `servicecodesview` AS select `s`.`id` AS `id`,`s`.`serviceid` AS `serviceid`,`s`.`attributeid` AS `attributeid`,`s`.`codeid` AS `codeid`,`a`.`acronym` AS `attributeacronym`,`a`.`name` AS `attributename`,`d`.`acronym` AS `codeacronym`,`d`.`name` AS `codename`,`d`.`errorcodeacronym` AS `errorcodeacronym`,`d`.`errorcodename` AS `errorcodename` from ((`servicecodes` `s` left join `dictionariesview` `d` on((`s`.`codeid` = `d`.`id`))) left join `dictionaries` `a` on((`s`.`attributeid` = `a`.`id`))) order by `s`.`serviceid`;

-- --------------------------------------------------------

--
-- Struktura widoku `serviceproductsview`
--
DROP TABLE IF EXISTS `serviceproductsview`;

CREATE OR REPLACE VIEW `serviceproductsview` AS select `sp`.`id` AS `id`,`sp`.`serviceid` AS `serviceid`,`sp`.`productid` AS `productid`,`p`.`name` AS `name`,`p`.`serial` AS `serial` from (`serviceproducts` `sp` left join `products` `p` on((`sp`.`productid` = `p`.`id`)));

-- --------------------------------------------------------

--
-- Struktura widoku `servicesview`
--
DROP TABLE IF EXISTS `servicesview`;

CREATE OR REPLACE VIEW `servicesview` AS select `s`.`id` AS `id`,`s`.`number` AS `number`,`s`.`warehouseid` AS `warehouseid`,`s`.`description` AS `description`,`s`.`parameters` AS `parameters`,`s`.`modifieddate` AS `modifieddate`,`s`.`equipment` AS `equipment`,`s`.`servicetypeid` AS `servicetypeid`,`s`.`typeid` AS `typeid`,`s`.`clientid` AS `clientid`,`s`.`dateadd` AS `dateadd`,`s`.`statusid` AS `statusid`,`s`.`planneddate` AS `planneddate`,`s`.`technicianid` AS `technicianid`,`s`.`timefrom` AS `timefrom`,`s`.`timetill` AS `timetill`,`s`.`regionid` AS `regionid`,`s`.`blockadecodeid` AS `blockadecodeid`,`s`.`laborcodeid` AS `laborcodeid`,`s`.`complaintcodeid` AS `complaintcodeid`,`s`.`calendarid` AS `calendarid`,`s`.`comments` AS `comments`,`s`.`areaid` AS `areaid`,`s`.`slots` AS `slots`,`s`.`products` AS `products`,`s`.`serialnumbers` AS `serialnumbers`,`s`.`macnumbers` AS `macnumbers`,`s`.`technicalcomments` AS `technicalcomments`,`s`.`coordinatorcomments` AS `coordinatorcomments`,`s`.`datefinished` AS `datefinished`,`s`.`performed` AS `performed`,concat('ul. ',`c`.`street`,' ',`c`.`streetno`,'/',`c`.`apartmentno`,', ',`c`.`postcode`,' ',`c`.`city`) AS `client`,`c`.`number` AS `clientnumber`,`c`.`city` AS `clientcity`,`c`.`street` AS `clientstreet`,`c`.`streetno` AS `clientstreetno`,`c`.`apartmentno` AS `clientapartment`,`c`.`postcode` AS `clientpostcode`,`c`.`homephone` AS `clienthomephone`,`c`.`workphone` AS `clientworkphone`,`c`.`cellphone` AS `clientcellphone`,concat(`u`.`lastname`,' ',`u`.`firstname`) AS `technician`,`ds`.`name` AS `status`,`ds`.`acronym` AS `statusacronym`,`dt`.`acronym` AS `servicetype`,(select group_concat(`ce`.`codeacronym` separator ',') from `servicecodesview` `ce` where ((`s`.`id` = `ce`.`serviceid`) and (`ce`.`attributeacronym` = 'errorcode'))) AS `errorcode`,(select group_concat(`cs`.`codeacronym` separator ',') from `servicecodesview` `cs` where ((`s`.`id` = `cs`.`serviceid`) and (`cs`.`attributeacronym` = 'solutioncode'))) AS `solutioncode`,(select group_concat(`cc`.`codeacronym` separator ',') from `servicecodesview` `cc` where ((`s`.`id` = `cc`.`serviceid`) and (`cc`.`attributeacronym` = 'cancellationcode'))) AS `cancellationcode`,(select group_concat(`cmi`.`codeacronym` separator ',') from `servicecodesview` `cmi` where ((`s`.`id` = `cmi`.`serviceid`) and (`cmi`.`attributeacronym` = 'modeminterchangecode'))) AS `modeminterchangecode`,(select group_concat(`cdi`.`codeacronym` separator ',') from `servicecodesview` `cdi` where ((`s`.`id` = `cdi`.`serviceid`) and (`cdi`.`attributeacronym` = 'decoderinterchangecode'))) AS `decoderinterchangecode`,(select group_concat(`ci`.`codeacronym` separator ',') from `servicecodesview` `ci` where ((`s`.`id` = `ci`.`serviceid`) and (`ci`.`attributeacronym` = 'installationcode'))) AS `installationcode`,`cal`.`acronym` AS `calendar`,`ar`.`acronym` AS `area`,`cb`.`acronym` AS `blockadecode`,`cl`.`acronym` AS `laborcode`,`cc2`.`acronym` AS `complaintcode`,(select group_concat(`sp`.`name` separator ',') from `serviceproductsview` `sp` where (`sp`.`serviceid` = `s`.`id`)) AS `productsreleased`,`dr`.`acronym` AS `region`,`sys`.`acronym` AS `system` from ((((((((((((`services` `s` left join `clients` `c` on((`s`.`clientid` = `c`.`id`))) left join `users` `u` on((`s`.`technicianid` = `u`.`id`))) left join `dictionaries` `ds` on((`s`.`statusid` = `ds`.`id`))) left join `dictionaries` `dt` on((`s`.`servicetypeid` = `dt`.`id`))) left join `dictionaries` `cal` on((`s`.`calendarid` = `cal`.`id`))) left join `dictionaries` `ar` on((`s`.`areaid` = `ar`.`id`))) left join `dictionaries` `r` on((`s`.`regionid` = `r`.`id`))) left join `dictionaries` `cb` on((`s`.`blockadecodeid` = `cb`.`id`))) left join `dictionaries` `cl` on((`s`.`laborcodeid` = `cl`.`id`))) left join `dictionaries` `cc2` on((`s`.`complaintcodeid` = `cc2`.`id`))) left join `dictionaries` `dr` on((`s`.`regionid` = `dr`.`id`))) left join `dictionaries` `sys` on((`s`.`systemid` = `sys`.`id`)));

-- --------------------------------------------------------

--
-- Struktura widoku `orderlinesview`
--
DROP TABLE IF EXISTS `orderlinesview`;

CREATE OR REPLACE VIEW `orderlinesview` AS select `p`.`warehouseid` AS `warehouseid`,`p`.`warehouse` AS `warehouse`,`ol`.`id` AS `id`,`ol`.`orderid` AS `orderid`,`ol`.`productid` AS `productid`,`ol`.`technicianid` AS `technicianid`,`ol`.`quantity` AS `quantity`,`ol`.`dateadd` AS `dateadd`,`ol`.`releasedate` AS `releasedate`,`ol`.`serviceid` AS `serviceid`,`ol`.`statusid` AS `statusid`,concat(`u`.`lastname`,' ',`u`.`firstname`) AS `technician`,`ds`.`name` AS `status`,`ds`.`acronym` AS `statusacronym`,`p`.`name` AS `product`,`p`.`serial` AS `serial`,`s`.`client` AS `client`,`s`.`clientid` AS `clientid`,`o`.`userid` AS `userid` from (((((`orderlines` `ol` left join `users` `u` on((`ol`.`technicianid` = `u`.`id`))) left join `dictionaries` `ds` on((`ol`.`statusid` = `ds`.`id`))) left join `productsview` `p` on((`ol`.`productid` = `p`.`id`))) left join `servicesview` `s` on((`ol`.`serviceid` = `s`.`id`))) left join `orders` `o` on((`ol`.`orderid` = `o`.`id`)));

-- --------------------------------------------------------

--
-- Struktura widoku `productsstatistics`
--
DROP TABLE IF EXISTS `productsstatistics`;

CREATE OR REPLACE VIEW `productsstatistics` AS select `orderlinesview`.`technicianid` AS `technicianid`,`orderlinesview`.`technician` AS `technician`,NULL AS `userid`,`orderlinesview`.`statusid` AS `statusid`,`orderlinesview`.`status` AS `status`,count(`orderlinesview`.`id`) AS `quantity` from `orderlinesview` group by `orderlinesview`.`technicianid`,`orderlinesview`.`technician`,`orderlinesview`.`statusid`,`orderlinesview`.`status` union all select NULL AS `NULL`,NULL AS `NULL`,`orderlinesview`.`userid` AS `userid`,`orderlinesview`.`statusid` AS `statusid`,`orderlinesview`.`status` AS `status`,count(`orderlinesview`.`id`) AS `quantity` from `orderlinesview` group by `orderlinesview`.`userid`,`orderlinesview`.`statusid`,`orderlinesview`.`status`;

-- --------------------------------------------------------

--
-- Struktura widoku `servicesstatistics`
--
DROP TABLE IF EXISTS `servicesstatistics`;

CREATE OR REPLACE VIEW `servicesstatistics` AS select `servicesview`.`technicianid` AS `technicianid`,`servicesview`.`technician` AS `technician`,`servicesview`.`statusid` AS `statusid`,`servicesview`.`status` AS `status`,count(`servicesview`.`id`) AS `quantity` from `servicesview` group by `servicesview`.`technicianid`,`servicesview`.`technician`,`servicesview`.`statusid`,`servicesview`.`status`;

-- --------------------------------------------------------

--
-- Struktura widoku `usersview`
--
DROP TABLE IF EXISTS `usersview`;

CREATE OR REPLACE VIEW `usersview` AS select `u`.`id` AS `id`,`u`.`symbol` AS `symbol`,`u`.`firstname` AS `firstname`,`u`.`lastname` AS `lastname`,`u`.`email` AS `email`,`u`.`password` AS `password`,`u`.`role` AS `role`,`u`.`admin` AS `admin`,`u`.`dateadd` AS `dateadd`,`u`.`statusid` AS `statusid`,`s`.`name` AS `status` from (`users` `u` left join `dictionaries` `s` on((`u`.`statusid` = `s`.`id`)));

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
