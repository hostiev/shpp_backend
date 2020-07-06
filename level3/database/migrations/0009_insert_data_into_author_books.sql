INSERT INTO `author_books` (`author_id`, `book_id`)
SELECT `authors`.`id`, `books`.`id`
FROM `authors`, `books`
WHERE `authors`.`name` = `books`.`author`