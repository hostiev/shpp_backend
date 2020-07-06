CREATE TABLE IF NOT EXISTS `books` (
    `id` INT UNSIGNED AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `author` VARCHAR(255) NOT NULL,
    `year` INT UNSIGNED NOT NULL,
    `about` VARCHAR(500) NOT NULL,
    `views` INT UNSIGNED NOT NULL,
    `clicks` INT UNSIGNED NOT NULL,
    `is_deleted` INT UNSIGNED NOT NULL,
    PRIMARY KEY (id)
)