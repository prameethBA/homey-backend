-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: fdb28.awardspace.net
-- Generation Time: Oct 25, 2020 at 12:38 PM
-- Server version: 5.7.20-log
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `3606050_homey`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `_id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `nic` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `boostedproperty`
--

CREATE TABLE `boostedproperty` (
  `property_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `amount` int(30) NOT NULL,
  `duration` varchar(20) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `_id` int(3) NOT NULL,
  `city` varchar(50) NOT NULL,
  `district_id` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`_id`, `city`, `district_id`) VALUES
(1, 'Eppawala', 1),
(2, 'Colombo - 07', 2);

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `comment_id` int(10) NOT NULL,
  `forum_id` int(10) NOT NULL,
  `comment` varchar(300) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `confirmationinfo`
--

CREATE TABLE `confirmationinfo` (
  `_id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `hash` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `confirmationinfo`
--

INSERT INTO `confirmationinfo` (`_id`, `user_id`, `hash`) VALUES
(2, 34, '09c218b2390e7454a30142d8992624d245f0b293cb135923a4dacf39d6556852');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `_id` tinyint(2) NOT NULL,
  `district` varchar(50) NOT NULL,
  `province_id` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`_id`, `district`, `province_id`) VALUES
(1, 'Anuradhapura', 0),
(2, 'Colombo', 0);

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `feature_id` int(10) NOT NULL,
  `feature_name` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `favourite`
--

CREATE TABLE `favourite` (
  `user_id` int(10) NOT NULL,
  `property_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `user_id` int(10) NOT NULL,
  `property_id` int(10) NOT NULL,
  `discription` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `forum`
--

CREATE TABLE `forum` (
  `forum_id` int(10) NOT NULL,
  `forum_details` varchar(300) NOT NULL,
  `time_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `forumpost`
--

CREATE TABLE `forumpost` (
  `forum_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `img_id` int(10) NOT NULL,
  `img` longtext NOT NULL,
  `property_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `user_id` int(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `user_status` tinyint(1) NOT NULL DEFAULT '0',
  `mobile` varchar(10) DEFAULT NULL,
  `access_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`user_id`, `email`, `password`, `user_status`, `mobile`, `access_token`) VALUES
(34, 'lakmalepp@gmail.com', '12bce374e7be15142e8172f668da00d8', 0, NULL, 'ew0KICAgICAgICAnYWxnJzogWydzaGExJywnbWQ1J10sDQogICAgICAgICd0eXAnOiAnSldUJw0KICAgIH0=.ew0KICAgICAgICAgICAgICAgICAgICBpZDogMzQsDQogICAgICAgICAgICAgICAgICAgIGVtYWlsOiAnbGFrbWFsZXBwQGdtYWlsLmNvbScNCiAgICAgICAgICAgICAgICB9.576956b5291ac38d04ef5f82cc974286a857f'),
(35, 'prameethmaduwantha@gmail.com', 'eb341c24c39d0b23d9981ff69c7711b5', 0, NULL, 'ew0KICAgICAgICAnYWxnJzogWydzaGExJywnbWQ1J10sDQogICAgICAgICd0eXAnOiAnSldUJw0KICAgIH0=.ew0KICAgICAgICAgICAgICAgICAgICBpZDogMzUsDQogICAgICAgICAgICAgICAgICAgIGVtYWlsOiAncHJhbWVldGhtYWR1d2FudGhhQGdtYWlsLmNvbScNCiAgICAgICAgICAgICAgICB9.576956b5291ac38d04ef5f82c');

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `property_id` int(10) NOT NULL,
  `property_title` varchar(100) NOT NULL,
  `property_price` int(30) NOT NULL,
  `key_money` int(30) NOT NULL,
  `min_period` varchar(50) NOT NULL,
  `avail_from` date NOT NULL,
  `property_type_id` int(10) NOT NULL,
  `discription` varchar(300) NOT NULL,
  `district` varchar(30) NOT NULL,
  `city` varchar(30) NOT NULL,
  `property_status` tinyint(1) NOT NULL,
  `feature_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `propertytype`
--

CREATE TABLE `propertytype` (
  `property_type_id` int(10) NOT NULL,
  `property_type_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `propertytype`
--

INSERT INTO `propertytype` (`property_type_id`, `property_type_name`) VALUES
(2, 'Annex'),
(4, 'Apartment'),
(5, 'Business/Office'),
(1, 'Home'),
(7, 'Mixed Use Buildings'),
(3, 'Room'),
(6, 'Warehouse');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `property_id` int(10) NOT NULL,
  `discription` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `_id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `nic` varchar(12) DEFAULT NULL,
  `user_image` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`_id`, `user_id`, `first_name`, `last_name`, `nic`, `user_image`) VALUES
(0, 34, 'Dimuthu', 'Lakmal', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `boostedproperty`
--
ALTER TABLE `boostedproperty`
  ADD PRIMARY KEY (`property_id`,`user_id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `city` (`city`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `confirmationinfo`
--
ALTER TABLE `confirmationinfo`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `hash` (`hash`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `district` (`district`),
  ADD UNIQUE KEY `_id` (`_id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`feature_id`),
  ADD UNIQUE KEY `feature_name` (`feature_name`);

--
-- Indexes for table `favourite`
--
ALTER TABLE `favourite`
  ADD PRIMARY KEY (`user_id`,`property_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`user_id`,`property_id`);

--
-- Indexes for table `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`forum_id`);

--
-- Indexes for table `forumpost`
--
ALTER TABLE `forumpost`
  ADD PRIMARY KEY (`forum_id`,`user_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`img_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD UNIQUE KEY `access_token` (`access_token`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `propertytype`
--
ALTER TABLE `propertytype`
  ADD PRIMARY KEY (`property_type_id`),
  ADD UNIQUE KEY `property_type_name` (`property_type_name`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `nic_3` (`nic`),
  ADD KEY `nic_2` (`nic`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `confirmationinfo`
--
ALTER TABLE `confirmationinfo`
  MODIFY `_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `_id` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `feature_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `user_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- AUTO_INCREMENT for table `propertytype`
--
ALTER TABLE `propertytype`
  MODIFY `property_type_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `confirmationinfo`
--
ALTER TABLE `confirmationinfo`
  ADD CONSTRAINT `confirmationinfo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `login` (`user_id`) ON DELETE NO ACTION ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
