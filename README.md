# utsLectureGIT
 eventmanagement

step 1 go to DBMS and copy paste the db init:

CREATE DATABASE event_mn;

use event_mn;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `event` (
  `event_id` int NOT NULL,
  `title` varchar(50) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `location` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `banner` varchar(255) DEFAULT '',
  `status` enum('active','completed','cancelled','upcoming') DEFAULT 'upcoming'
);



CREATE TABLE `event_img` (
  `event_img_id` int NOT NULL,
  `event_id` int DEFAULT NULL,
  `event_img` varchar(255) DEFAULT NULL
);



CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(70) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0'
);


CREATE TABLE `user_event` (
  `event_id` int NOT NULL,
  `user_id` int NOT NULL
);


ALTER TABLE `event`
  ADD PRIMARY KEY (`event_id`);


ALTER TABLE `event_img`
  ADD PRIMARY KEY (`event_img_id`),
  ADD KEY `event_id` (`event_id`);


ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);


ALTER TABLE `user_event`
  ADD PRIMARY KEY (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);


ALTER TABLE `event`
  MODIFY `event_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;


ALTER TABLE `event_img`
  MODIFY `event_img_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT;


ALTER TABLE `event_img`
  ADD CONSTRAINT `event_img_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE;


ALTER TABLE `user_event`
  ADD CONSTRAINT `user_event_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_event_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;
COMMIT;

step 2: go to db.php and modify config according to ur db setup
step 3: go to index.php
step 4: go to composer.json
step 5: change "name": "kevin/utslec2_copy", to ur folder/utslec2_copy