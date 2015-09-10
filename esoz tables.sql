-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 28 Cze 2015, 16:16
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
-- Struktura tabeli dla tabeli `acl`
--

CREATE TABLE IF NOT EXISTS `acl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `privilegeid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `clients`
--

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(255) NOT NULL COMMENT 'miasto',
  `street` varchar(255) NOT NULL COMMENT 'ulica',
  `number` varchar(25) NOT NULL COMMENT 'numer',
  `postcode` int(5) NOT NULL COMMENT 'kod pocztowy',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data dodania',
  `streetno` int(11) NOT NULL COMMENT 'numer domu',
  `apartmentno` int(11) NOT NULL COMMENT 'numer mieszkania',
  `homephone` int(11) DEFAULT NULL,
  `workphone` int(11) DEFAULT NULL COMMENT 'telefon domowy',
  `cellphone` int(11) DEFAULT NULL COMMENT 'telefon do pracy',
  `statusid` int(11) NOT NULL DEFAULT '0' COMMENT 'telefon komórkowy',
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `statusid` (`statusid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dictionaries`
--

CREATE TABLE IF NOT EXISTS `dictionaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL DEFAULT '0' COMMENT 'nadrzędny',
  `acronym` varchar(255) NOT NULL COMMENT 'akronim',
  `name` varchar(1000) NOT NULL COMMENT 'nazwa',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data dodania',
  `statusid` int(11) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  UNIQUE KEY `parentid` (`parentid`,`acronym`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dictionaryattributes`
--

CREATE TABLE IF NOT EXISTS `dictionaryattributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryid` int(11) NOT NULL COMMENT 'wpis słownika',
  `attributeid` int(11) NOT NULL COMMENT 'atrybut',
  `value` varchar(255) DEFAULT NULL COMMENT 'wartość',
  PRIMARY KEY (`id`),
  KEY `entryid` (`entryid`),
  KEY `attributeid` (`attributeid`),
  KEY `value` (`value`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tablename` varchar(255) DEFAULT NULL,
  `rowid` int(11) DEFAULT NULL,
  `columnname` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orderlines`
--

CREATE TABLE IF NOT EXISTS `orderlines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL COMMENT 'identyfikator zamówienia',
  `productid` int(11) NOT NULL COMMENT 'identyfikator produktu',
  `technicianid` int(11) NOT NULL COMMENT 'technik',
  `quantity` int(11) NOT NULL COMMENT 'ilość',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data dodania',
  `releasedate` timestamp NULL DEFAULT NULL COMMENT 'data wydania',
  `serviceid` int(11) NOT NULL COMMENT 'identyfikator zlecenia',
  `statusid` int(11) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  KEY `orderid` (`orderid`),
  KEY `statusid` (`statusid`),
  KEY `serviceid` (`serviceid`),
  KEY `technicianid` (`technicianid`),
  KEY `productid` (`productid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT 'właściciel',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data utworzenia',
  `statusid` int(11) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `privileges`
--

CREATE TABLE IF NOT EXISTS `privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(255) NOT NULL,
  `controller` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `modifydate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `warehouseid` int(11) NOT NULL COMMENT 'magazyn',
  `acronym` varchar(255) NOT NULL COMMENT 'kod produktu',
  `name` varchar(1000) NOT NULL COMMENT 'nazwa produktu',
  `description` text NOT NULL COMMENT 'opis',
  `unitid` int(11) NOT NULL COMMENT 'jednostka',
  `quantity` int(11) NOT NULL COMMENT 'ilosc',
  `qtyreserved` int(11) NOT NULL COMMENT 'ilość zarezerwowana',
  `qtyreleased` int(11) NOT NULL COMMENT 'ilość wydana',
  `qtyreturned` int(11) NOT NULL COMMENT 'ilość zwrócona',
  `qtyavailable` int(11) NOT NULL COMMENT 'ilość dostępna',
  `serial` varchar(255) NOT NULL COMMENT 'numer seryjny',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data dodania',
  `statusid` int(11) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial` (`serial`),
  KEY `statusid` (`statusid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `servicecodes`
--

CREATE TABLE IF NOT EXISTS `servicecodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serviceid` int(11) NOT NULL COMMENT 'zgłoszenie',
  `attributeid` int(11) NOT NULL COMMENT 'typ kodu',
  `codeid` int(11) NOT NULL COMMENT 'kod',
  PRIMARY KEY (`id`),
  KEY `serviceid` (`serviceid`),
  KEY `attributeid` (`attributeid`),
  KEY `codeid` (`codeid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `serviceproducts`
--

CREATE TABLE IF NOT EXISTS `serviceproducts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serviceid` int(11) NOT NULL COMMENT 'zgłoszenie',
  `productid` int(11) NOT NULL COMMENT 'produkt',
  PRIMARY KEY (`id`),
  KEY `serviceid` (`serviceid`),
  KEY `productid` (`productid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `servicereports`
--

CREATE TABLE IF NOT EXISTS `servicereports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` int(11) NOT NULL COMMENT 'rodzaj zleceń',
  `userid` int(11) NOT NULL COMMENT 'użytkownik',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data dodania',
  `planneddate` date NOT NULL COMMENT 'planowana data',
  `file` varchar(255) NOT NULL COMMENT 'plik',
  `senddate` timestamp NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'data wysłania',
  `statusid` int(11) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `services`
--

CREATE TABLE IF NOT EXISTS `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL COMMENT 'numer zlecenia',
  `description` text COMMENT 'opis zgłoszenia w REMEDY',
  `parameters` text COMMENT 'parametry sygnału',
  `modifieddate` date DEFAULT NULL COMMENT 'data ostatniej zmiany',
  `warehouseid` int(11) DEFAULT NULL COMMENT 'magazyn',
  `servicetypeid` int(11) NOT NULL COMMENT 'rodzaj zlecenia',
  `typeid` int(11) NOT NULL COMMENT 'typ zlecenia',
  `clientid` int(11) NOT NULL COMMENT 'klient',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data dodania',
  `statusid` int(11) NOT NULL COMMENT 'status',
  `planneddate` date NOT NULL COMMENT 'planowana data realizacji usługi',
  `technicianid` int(11) DEFAULT NULL COMMENT 'technik',
  `timefrom` time DEFAULT NULL COMMENT 'od godziny',
  `timetill` time DEFAULT NULL COMMENT 'do godziny',
  `regionid` int(11) NOT NULL COMMENT 'obszar dyspozytorski',
  `systemid` int(11) NOT NULL COMMENT 'system',
  `blockadecodeid` int(11) NOT NULL COMMENT 'kod blokady',
  `laborcodeid` int(11) NOT NULL COMMENT 'kod pracy',
  `complaintcodeid` int(11) NOT NULL COMMENT 'kod skargi',
  `calendarid` int(11) NOT NULL COMMENT 'kalendarz serwisowy',
  `comments` varchar(4000) NOT NULL COMMENT 'komentarz do zlecenia',
  `areaid` int(11) NOT NULL COMMENT 'rejon',
  `slots` varchar(32) NOT NULL COMMENT 'gniazda',
  `products` varchar(4000) NOT NULL COMMENT 'posiadane produkty',
  `equipment` text COMMENT 'nazwa sprzętu',
  `serialnumbers` varchar(4000) NOT NULL COMMENT 'numery seryjne sprzętu',
  `macnumbers` varchar(4000) NOT NULL COMMENT 'numery MAC sprzętu',
  `technicalcomments` varchar(4000) NOT NULL COMMENT 'komentarz technika',
  `coordinatorcomments` varchar(400) NOT NULL COMMENT 'komentarz koordynatora',
  `datefinished` timestamp NULL DEFAULT NULL COMMENT 'data i godzina zakończenia',
  `performed` int(11) DEFAULT NULL COMMENT 'czy wykonane',
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `warehouseid` (`warehouseid`),
  KEY `servicetypeid` (`servicetypeid`),
  KEY `typeid` (`typeid`),
  KEY `clientid` (`clientid`),
  KEY `statusid` (`statusid`),
  KEY `technicianid` (`technicianid`),
  KEY `planneddate` (`planneddate`),
  KEY `systemid` (`systemid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(64) NOT NULL COMMENT 'symbol',
  `firstname` varchar(255) NOT NULL COMMENT 'imie',
  `lastname` varchar(255) NOT NULL COMMENT 'nazwisko',
  `email` varchar(255) NOT NULL COMMENT 'email',
  `password` varchar(255) NOT NULL COMMENT 'haslo',
  `role` varchar(255) NOT NULL COMMENT 'rola',
  `admin` int(11) NOT NULL,
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data dodania',
  `statusid` int(11) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `warehouses`
--

CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parentid` int(11) NOT NULL COMMENT 'magazyn nadrzedny',
  `typeid` int(11) NOT NULL COMMENT 'typ magazynu',
  `areaid` int(11) NOT NULL COMMENT 'rejon',
  `name` varchar(255) NOT NULL COMMENT 'nazwa magazynu',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'data dodania',
  `statusid` int(11) NOT NULL COMMENT 'status',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
