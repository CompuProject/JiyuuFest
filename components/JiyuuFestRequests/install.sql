CREATE TABLE IF NOT EXISTS `JiyuuFest` (
  `fest` VARCHAR(50) NOT NULL,
  `code` VARCHAR(3) NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `regulations` LONGTEXT NOT NULL,
  `filingRequest_Intramural_Start` DATETIME NOT NULL,
  `filingRequest_Intramural_Stop` DATETIME NOT NULL,
  `filingRequest_Intramural_End` DATETIME NOT NULL,
  `filingRequest_Extramural_Start` DATETIME NOT NULL,
  `filingRequest_Extramural_Stop` DATETIME NOT NULL,
  `filingRequest_Extramural_End` DATETIME NOT NULL,
  `festivalStart` DATETIME NULL,
  `festivalDay` DATE NULL,
  PRIMARY KEY (`fest`),
  UNIQUE INDEX `code_UNIQUE` (`code` ASC))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequestType` (
  `type` VARCHAR(50) NOT NULL,
  `code` VARCHAR(2) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` LONGTEXT NULL,
  `tableName` VARCHAR(200) NOT NULL,
  `sequence` INT(2) UNSIGNED NOT NULL,
  `minNumberOfParticipants` INT(2) NOT NULL DEFAULT 1,
  `maxNumberOfParticipants` INT(2) UNSIGNED NOT NULL,
  `minDurationMinutes` INT(2) UNSIGNED NULL DEFAULT NULL,
  `minDurationSeconds` INT(2) UNSIGNED NULL DEFAULT NULL,
  `maxDurationMinutes` INT(2) UNSIGNED NULL DEFAULT NULL,
  `maxDurationSeconds` INT(2) UNSIGNED NULL DEFAULT NULL,
  `mayBeContest` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `characterName` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `photo` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `original` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `intramural` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`type`),
  UNIQUE INDEX `sequence_UNIQUE` (`sequence` ASC),
  UNIQUE INDEX `tableName_UNIQUE` (`tableName` ASC),
  UNIQUE INDEX `code_UNIQUE` (`code` ASC))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequestStatus` (
  `status` VARCHAR(50) NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(45) NOT NULL,
  `order` INT(2) NOT NULL,
  PRIMARY KEY (`status`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest` (
  `request` VARCHAR(200) NOT NULL,
  `contest` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `createdFor` VARCHAR(25) NOT NULL,
  `created` DATETIME NOT NULL,
  `changed` DATETIME NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `fest` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) NOT NULL,
  `numberOfParticipants` INT(2) UNSIGNED NOT NULL DEFAULT 1,
  `durationMin` INT(2) UNSIGNED NULL DEFAULT NULL,
  `durationSec` INT(2) UNSIGNED NULL DEFAULT NULL,
  `wish` LONGTEXT NULL,
  PRIMARY KEY (`request`),
  INDEX `fk_JiyuuFestRequest_1_idx` (`createdFor` ASC),
  INDEX `fk_JiyuuFestRequest_2_idx` (`type` ASC),
  INDEX `fk_JiyuuFestRequest_3_idx` (`fest` ASC),
  INDEX `fk_JiyuuFestRequest_4_idx` (`status` ASC),
  CONSTRAINT `fk_JiyuuFestRequest_1`
    FOREIGN KEY (`createdFor`)
    REFERENCES `Users` (`login`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_JiyuuFestRequest_2`
    FOREIGN KEY (`type`)
    REFERENCES `JiyuuFestRequestType` (`type`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_JiyuuFestRequest_3`
    FOREIGN KEY (`fest`)
    REFERENCES `JiyuuFest` (`fest`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_JiyuuFestRequest_4`
    FOREIGN KEY (`status`)
    REFERENCES `JiyuuFestRequestStatus` (`status`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JRequestUsers` (
  `request` VARCHAR(200) NOT NULL,
  `user` VARCHAR(25) NOT NULL,
  `confirmed` TINYINT(1) UNSIGNED NOT NULL,
  `characterName` VARCHAR(100) NULL,
  `photo` VARCHAR(200) NULL,
  `original` VARCHAR(200) NULL,
  PRIMARY KEY (`request`, `user`),
  INDEX `fk_JRequestUsers_2_idx` (`user` ASC),
  CONSTRAINT `fk_JRequestUsers_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_JRequestUsers_2`
    FOREIGN KEY (`user`)
    REFERENCES `Users` (`login`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_Karaoke` (
  `request` VARCHAR(200) NOT NULL,
  `songTitle` VARCHAR(100) NOT NULL,
  `artistSongs` VARCHAR(100) NOT NULL,
  `kosbend` VARCHAR(100) NULL,
  `demo` VARCHAR(200) NULL,
  `audition` TINYINT(1) NOT NULL DEFAULT 0,
  `audio` VARCHAR(200) NULL,
  `instrumental` TINYINT(1) NOT NULL DEFAULT 0,
  `audioInVideo` TINYINT(1) NOT NULL DEFAULT 0,
  `video` VARCHAR(200) NULL,
  `noVideo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`request`),
  CONSTRAINT `fk_JiyuuFestRequest_Karaoke_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_Dance` (
  `request` VARCHAR(200) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `kosbend` VARCHAR(100) NULL,
  `demo` VARCHAR(200) NULL,
  `audition` TINYINT(1) NOT NULL DEFAULT 0,
  `audio` VARCHAR(200) NULL,
  `instrumental` TINYINT(1) NOT NULL DEFAULT 0,
  `audioInVideo` TINYINT(1) NOT NULL DEFAULT 0,
  `video` VARCHAR(200) NULL,
  `noVideo` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`request`),
  CONSTRAINT `fk_JiyuuFestRequest_Dance_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_Scene` (
  `request` VARCHAR(200) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `fendom` VARCHAR(100) NULL,
  `kosbend` VARCHAR(100) NULL,
  `demo` VARCHAR(200) NULL,
  `audition` TINYINT(1) NOT NULL DEFAULT 0,
  `audio` VARCHAR(200) NULL,
  `instrumental` TINYINT(1) NOT NULL DEFAULT 0,
  `audioInVideo` TINYINT(1) NOT NULL DEFAULT 0,
  `video` VARCHAR(200) NULL,
  `noVideo` TINYINT(1) NOT NULL DEFAULT 1,
  `scenario` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`request`),
  CONSTRAINT `fk_JiyuuFestRequest_Scene_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_DefileType` (
  `type` VARCHAR(100) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `description` LONGTEXT NOT NULL,
  `fendom` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `characterName` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `photo` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `original` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`type`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_ActionDefile` (
  `request` VARCHAR(200) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `fendom` VARCHAR(100) NULL,
  `kosbend` VARCHAR(100) NULL,
  `type` VARCHAR(100) NOT NULL,
  `demo` VARCHAR(200) NULL,
  `audition` TINYINT(1) NOT NULL DEFAULT 0,
  `audio` VARCHAR(200) NOT NULL,
  `instrumental` TINYINT(1) NOT NULL DEFAULT 0,
  `audioInVideo` TINYINT(1) NOT NULL DEFAULT 0,
  `video` VARCHAR(200) NULL,
  `noVideo` TINYINT(1) NOT NULL DEFAULT 1,
  `collage` VARCHAR(200) NOT NULL,
  `explication` VARCHAR(200) NULL,
  PRIMARY KEY (`request`),
  INDEX `fk_JiyuuFestRequest_ActionDefile_1_idx` (`type` ASC),
  CONSTRAINT `fk_JiyuuFestRequest_ActionDefile_2`
    FOREIGN KEY (`type`)
    REFERENCES `JiyuuFestRequest_DefileType` (`type`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_JiyuuFestRequest_ActionDefile_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_Defile` (
  `request` VARCHAR(200) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `fendom` VARCHAR(100) NULL,
  `kosbend` VARCHAR(100) NULL,
  `audio` VARCHAR(200) NOT NULL,
  `type` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`request`),
  INDEX `fk_JiyuuFestRequest_Defile_1_idx` (`type` ASC),
  CONSTRAINT `fk_JiyuuFestRequest_Defile_2`
    FOREIGN KEY (`type`)
    REFERENCES `JiyuuFestRequest_DefileType` (`type`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_JiyuuFestRequest_Defile_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_VideoCosplay` (
  `request` VARCHAR(200) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `fendom` VARCHAR(100) NOT NULL,
  `characters` LONGTEXT NOT NULL,
  `musicTracks` LONGTEXT NOT NULL,
  `programs` LONGTEXT NOT NULL,
  `videographer` VARCHAR(100) NOT NULL,
  `video` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`request`),
  CONSTRAINT `fk_JiyuuFestRequest_VideoCosplay_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_Photo` (
  `request` VARCHAR(200) NOT NULL,
  `title` VARCHAR(100) NULL,
  `fendom` VARCHAR(200) NOT NULL,
  `characters` LONGTEXT NOT NULL,
  `photographer` VARCHAR(100) NOT NULL,
  `photo1` VARCHAR(200) NOT NULL,
  `photo2` VARCHAR(200) NULL,
  `photo3` VARCHAR(200) NULL,
  `photo4` VARCHAR(200) NULL,
  PRIMARY KEY (`request`),
  CONSTRAINT `fk_JiyuuFestRequest_Photo_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_Image` (
  `request` VARCHAR(200) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `image` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`request`),
  CONSTRAINT `fk_JiyuuFestRequest_Image_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `JiyuuFestRequest_AMV` (
  `request` VARCHAR(200) NOT NULL,
  `title` VARCHAR(100) NOT NULL,
  `fendom` VARCHAR(100) NOT NULL,
  `musicTracks` LONGTEXT NOT NULL,
  `programs` LONGTEXT NOT NULL,
  `amv` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`request`),
  CONSTRAINT `fk_JiyuuFestRequest_AMV_1`
    FOREIGN KEY (`request`)
    REFERENCES `JiyuuFestRequest` (`request`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;



SELECT 
JFR.`request`
FROM `JiyuuFestRequest` as JFR 
LEFT JOIN `JiyuuFestRequestUsers` as JFRU 
on JFR.`request` = JFRU.`request`
where `contest`='1'
GROUP BY JFR.`request`