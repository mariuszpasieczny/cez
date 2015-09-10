-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Czas wygenerowania: 28 Cze 2015, 16:55
-- Wersja serwera: 5.5.21-log
-- Wersja PHP: 5.3.20

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Baza danych: `esoz2`
--

--
-- Zrzut danych tabeli `dictionaries`
--

INSERT INTO `dictionaries` (`id`, `parentid`, `acronym`, `name`, `dateadd`, `statusid`) VALUES
(1, 0, '', '', '2015-06-28 14:35:31', 0),
(2, 1, 'attribute', 'Atrybuty', '2015-06-28 14:35:52', 0),
(3, 1, 'dictionary', 'Słowniki', '2015-06-28 14:36:10', 0),
(4, 1, 'status', 'Statusy', '2015-06-28 14:36:21', 0),
(5, 1, 'type', 'Typy', '2015-06-28 14:36:33', 0),
(6, 4, 'service', 'Statusy zlecen', '2015-06-28 14:53:19', 0),
(7, 6, 'new', 'Nowe', '2015-06-28 14:53:19', 0),
(8, 6, 'assigned', 'Przypisane', '2015-06-28 14:53:19', 0),
(9, 6, 'finished', 'Zakonczone', '2015-06-28 14:53:19', 0),
(10, 6, 'closed', 'Zamkniete', '2015-06-28 14:53:19', 0),
(11, 6, 'deleted', 'Usuniete', '2015-06-28 14:53:19', 0),
(12, 6, 'reassigned', 'Ponownie przypisane', '2015-06-28 14:53:19', 0),
(13, 6, 'withdrawn', 'Wycofane', '2015-06-28 14:53:19', 0),
(14, 4, 'products', 'Statusy produktow', '2015-06-28 14:53:19', 0),
(15, 14, 'instock', 'Na stanie', '2015-06-28 14:53:19', 0),
(16, 14, 'reserved', 'W koszyku', '2015-06-28 14:53:19', 0),
(17, 14, 'released', 'Wydane', '2015-06-28 14:53:19', 0),
(18, 14, 'deleted', 'Usuniete', '2015-06-28 14:53:19', 0),
(19, 4, 'orders', 'Statusy zamowien', '2015-06-28 14:53:19', 0),
(20, 19, 'new', 'Nowe', '2015-06-28 14:53:19', 0),
(21, 19, 'assigned', 'Przypisane', '2015-06-28 14:53:19', 0),
(22, 19, 'released', 'Wydane technikowi', '2015-06-28 14:53:20', 0),
(23, 19, 'invoiced', 'Wydane klientowi', '2015-06-28 14:53:20', 0),
(24, 19, 'returned', 'Zwrocone', '2015-06-28 14:53:20', 0),
(25, 19, 'deleted', 'UsuniÄte', '2015-06-28 14:53:20', 0),
(26, 4, 'users', 'Statusy uzytkownikow', '2015-06-28 14:53:20', 0),
(27, 26, 'active', 'Aktywny', '2015-06-28 14:53:20', 0),
(28, 26, 'inactive', 'Nieaktywny', '2015-06-28 14:53:20', 0),
(29, 26, 'deleted', 'UsuniÄty', '2015-06-28 14:53:20', 0),
(30, 5, 'service', 'Typy zlecen', '2015-06-28 14:53:20', 0),
(31, 30, 'service', 'Serwisowe', '2015-06-28 14:53:20', 0),
(32, 30, 'installation', 'Instalacyjne', '2015-06-28 14:53:20', 0),
(33, 30, 'disconnection', 'Odlaczeniowe', '2015-06-28 14:53:20', 0),
(34, 3, 'warehouse', 'Slowniki dla magazynu', '2015-06-28 14:53:20', 0),
(35, 34, 'type', 'Typy magazynow', '2015-06-28 14:53:20', 0),
(36, 35, 'INST', 'Instalacje', '2015-06-28 14:53:20', 0),
(37, 35, 'SERV', 'Serwis', '2015-06-28 14:53:20', 0),
(38, 34, 'area', 'Regiony magazynow', '2015-06-28 14:53:20', 0),
(39, 38, 'LBL', 'Lublin', '2015-06-28 14:53:20', 0),
(40, 34, 'unit', 'Jednostki', '2015-06-28 14:53:20', 0),
(41, 40, 'szt', 'sztuki', '2015-06-28 14:53:20', 0),
(42, 40, 'mb', 'metr biezacy', '2015-06-28 14:53:20', 0),
(43, 3, 'service', 'Slowniki dla zlecen', '2015-06-28 14:53:20', 0),
(44, 43, 'region', 'Obszar dyspozytorski', '2015-06-28 14:53:20', 0),
(45, 43, 'system', 'System', '2015-06-28 14:53:20', 0),
(46, 43, 'laborcode', 'Kod pracy', '2015-06-28 14:53:20', 0),
(47, 43, 'complaintcode', 'Kod skargi', '2015-06-28 14:53:20', 0),
(48, 43, 'blockadecode', 'Kod blokady', '2015-06-28 14:53:20', 0),
(49, 43, 'calendar', 'Kalendarz serwisowy', '2015-06-28 14:53:20', 0),
(50, 43, 'area', 'Rejon', '2015-06-28 14:53:20', 0),
(51, 43, 'type', 'Typ zlecenia', '2015-06-28 14:53:20', 0),
(52, 43, 'errorcode', 'Kod bledu', '2015-06-28 14:53:20', 0),
(53, 43, 'solutioncode', 'Kod rozwiazania', '2015-06-28 14:53:20', 0),
(54, 43, 'modeminterchangecode', 'Kod wymiany modemu', '2015-06-28 14:53:20', 0),
(55, 43, 'decoderinterchangecode', 'Kod wymiany dekodera', '2015-06-28 14:53:20', 0),
(56, 43, 'cancellationcode', 'Kod odwolania', '2015-06-28 14:53:20', 0),
(57, 43, 'installationcode', 'Kod instalacyjny', '2015-06-28 14:53:20', 0),
(58, 2, 'price', 'Cena', '2015-06-28 14:53:20', 0),
(59, 2, 'datefrom', 'Data od', '2015-06-28 14:53:20', 0),
(60, 2, 'datetill', 'Data do', '2015-06-28 14:53:20', 0),
(61, 2, 'errorcodeid', 'Kod bledu', '2015-06-28 14:53:20', 0);

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `symbol`, `firstname`, `lastname`, `email`, `password`, `role`, `admin`, `dateadd`, `statusid`) VALUES
(1, 'ADM', 'admin', 'admin', 'admin@localhost.pl', 'd41d8cd98f00b204e9800998ecf8427e', 'admin', 0, '2015-06-28 14:41:53', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
