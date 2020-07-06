<?php


namespace app\models;


use app\core\Config;
use app\core\Model;
use PDO;

/**
 * Manages books data.
 */
class BooksModel extends Model {

    /**
     * Gets book by the specified id.
     * @param $id
     * @return mixed
     */
    public function getBookById($id) {
        // Increasing views counter
        $statement = $this->connection->prepare("
            UPDATE `books`
            SET `views` = `views` + 1
            WHERE `id` = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();

        // Getting book info by id
        $statement = $this->connection->prepare("
            SELECT `b`.`id`, `b`.`name`, `b`.`year`, `b`.`about`, GROUP_CONCAT(`a`.`name` SEPARATOR ', ') 
            AS `author`
            FROM `books` AS `b`
            LEFT JOIN `author_books` AS `ab` ON `ab`.`book_id` = `b`.`id`
            LEFT JOIN `authors`AS `a` ON `a`.`id` = `ab`.`author_id`
            WHERE `b`.`id` = :id
            AND `b`.`is_deleted` = 0
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC)[0];
    }

    /**
     * Gets books according to the specified offset and page.
     * @param $parameters
     * @return mixed
     */
    public function getBooks($parameters) {
        $offset = array_key_exists('offset', $parameters) ? $parameters['offset']
            : Config::getInstance()->getConfig('offset');
        $page = array_key_exists('page', $parameters) ? $parameters['page'] : 1;
        $start = $page * $offset - $offset;

        $statement = $this->connection->prepare("
            SELECT `b`.`id`, `b`.`name`, `b`.`year`, `b`.`about`, `b`.`views`, `b`.`clicks`, 
                   GROUP_CONCAT(`a`.`name` SEPARATOR ', ') 
            AS `author`
            FROM `books` AS `b`
            LEFT JOIN `author_books` AS `ab` 
            ON `ab`.`book_id` = `b`.`id`
            LEFT JOIN `authors`AS `a` 
            ON `a`.`id` = `ab`.`author_id`
            WHERE `b`.`is_deleted` = 0
            GROUP BY `b`.`id`
            LIMIT :start, :offset
        ");
        $statement->bindValue(':start', $start, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        // Forming output result
        $result['books'] = $statement->fetchAll(PDO::FETCH_ASSOC);
        $booksCount = $this->connection->query("
            SELECT COUNT(*)
            FROM `books`
        ")->fetchColumn();
        $result['booksCount'] = $booksCount;
        $result['page'] = $page;
        $result['offset'] = $offset;

        return $result;
    }

    /**
     * Adds a new book.
     * @param $bookInfo
     */
    public function addBook($bookInfo) {
        $name = $bookInfo['title'];
        $authors[] = $bookInfo['author1'];
        $authors[] = $bookInfo['author2'];
        $authors[] = $bookInfo['author3'];
        $year = $bookInfo['year'];
        $about = $bookInfo['about'];

        // Adding book info
        $statement = $this->connection->prepare("
            INSERT INTO `books` (`name`, `year`, `about`, `views`, `clicks`, `is_deleted`)
            VALUES (:name, :year, :about, 0, 0, 0)
        ");
        $statement->bindValue(':name', $name);
        $statement->bindValue(':year', $year);
        $statement->bindValue(':about', $about);
        $statement->execute();

        $lastBookID = $this->connection->query("
            SELECT MAX(`id`) 
            FROM `books`")->fetchColumn();

        // Adding authors and forming relations
        foreach ($authors as $author) {
            // Getting id if author already exists
            $statement = $this->connection->prepare("
                SELECT `id`
                FROM `authors`
                WHERE `name` = :author
            ");
            $statement->bindValue(':author', $author);
            $statement->execute();

            $lastAuthorID = $statement->fetchColumn();

            // ... if not - adding new author and getting id
            if (!$lastAuthorID) {
                $statement = $this->connection->prepare("
                    INSERT INTO `authors` (`name`)
                    VALUES (:author)
                ");
                $statement->bindValue(':author', $author);
                $statement->execute();

                $lastAuthorID = $this->connection->query("
                    SELECT MAX(`id`) 
                    FROM `authors`")->fetchColumn();
            }

            // Adding author id - book id relation
            $this->connection->query("
                INSERT INTO `author_books` (`author_id`, `book_id`)
                VALUES ($lastAuthorID, $lastBookID)
            ");
        }
    }

    /**
     * Uploads book image to the server.
     * @param $image
     */
    public function uploadImage($image) {
        $statement = $this->connection->query("
            SELECT MAX(`id`)
            FROM `books`
        ");
        $lastID = $statement->fetch(PDO::FETCH_COLUMN);
        move_uploaded_file($image['tmp_name'], '../public/assets/books-page_files/' . $lastID . '.jpg');
    }

    /**
     * Mark the specified book  and its relation to authors as deleted.
     * @param $id
     */
    public function deleteBook($id) {
        $bookID = $id;
        $statement = $this->connection->prepare("
            UPDATE `books`
            SET `is_deleted` = 1
            WHERE `id` = :bookID;

            UPDATE `author_books`
            SET `is_deleted` = 1
            WHERE `book_id` = :bookID;
        ");
        $statement->bindValue(':bookID', $bookID);
        $statement->execute();
    }

    /**
     * Searches books by the specified search query in parameters.
     * @param $parameters
     * @return mixed
     */
    public function searchBooks($parameters) {
        $offset = array_key_exists('offset', $parameters) ? $parameters['offset']
            : Config::getInstance()->getConfig('offset');
        $page = array_key_exists('page', $parameters) ? $parameters['page'] : 1;
        $start = $page * $offset - $offset;
        $searchPattern = '%' . $parameters['search'] . '%';

        // Getting searched books, limited by the current page and offset
        $statement = $this->connection->prepare("
            SELECT *
            FROM (
                SELECT `b`.`id`, `b`.`name`, `b`.`year`, `b`.`about`, GROUP_CONCAT(`a`.`name` SEPARATOR ', ') 
                AS `author`
                FROM `books` AS `b`
                LEFT JOIN `author_books` AS `ab` 
                ON `ab`.`book_id` = `b`.`id`
                LEFT JOIN `authors`AS `a` 
                ON `a`.`id` = `ab`.`author_id`
                WHERE `b`.`is_deleted` = 0
                GROUP BY `b`.`id`) AS searchResult
            WHERE `name` LIKE :searchPattern 
            OR `author` LIKE :searchPattern
            OR `year` LIKE :searchPattern
            OR `about` LIKE :searchPattern
            LIMIT :start, :offset
        ");
        $statement->bindValue(':searchPattern', $searchPattern);
        $statement->bindValue(':start', $start, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        $result['books'] = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Counting overall search results
        $statement = $this->connection->prepare("
            SELECT COUNT(*)
            FROM (
                SELECT `b`.`id`, `b`.`name`, `b`.`year`, `b`.`about`, GROUP_CONCAT(`a`.`name` SEPARATOR ', ') 
                AS `author`
                FROM `books` AS `b`
                LEFT JOIN `author_books` AS `ab` 
                ON `ab`.`book_id` = `b`.`id`
                LEFT JOIN `authors`AS `a` 
                ON `a`.`id` = `ab`.`author_id`
                WHERE `b`.`is_deleted` = 0
                GROUP BY `b`.`id`) AS searchResult
            WHERE `name` LIKE :searchPattern 
            OR `author` LIKE :searchPattern
            OR `year` LIKE :searchPattern
            OR `about` LIKE :searchPattern
        ");
        $statement->bindValue(':searchPattern', $searchPattern);
        $statement->execute();

        $booksCount = $statement->fetchColumn();
        $result['booksCount'] = $booksCount;
        $result['page'] = $page;
        $result['offset'] = $offset;

        return $result;
    }

    /**
     * Increases the clicks counter.
     * @param $id
     */
    public function increaseClicks($id) {
        $statement = $this->connection->prepare("
            UPDATE `books`
            SET `clicks` = `clicks` + 1
            WHERE `id` = :id
        ");
        $statement->bindValue(':id', $id);
        $statement->execute();
    }
}