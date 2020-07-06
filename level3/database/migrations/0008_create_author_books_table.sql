CREATE TABLE IF NOT EXISTS `author_books` (
     `id` INT UNSIGNED AUTO_INCREMENT,
     `author_id` INT UNSIGNED,
     `book_id` INT UNSIGNED,
     `is_deleted` INT UNSIGNED NOT NULL DEFAULT 0,
     PRIMARY KEY (id)
)