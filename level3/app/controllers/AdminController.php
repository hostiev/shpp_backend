<?php

namespace app\controllers;

use app\core\Controller;

/**
 * Manages admin page actions.
 */
class AdminController extends Controller {

    const BOOKS_MODEL_NAME = 'Books';
    private $booksModel;

    /**
     * Constructs and gets BooksModel.
     * @param $route
     */
    public function __construct($route)
    {
        parent::__construct($route);
        $modelPath = 'app\models\\' . self::BOOKS_MODEL_NAME . 'Model';
        $this->booksModel = new $modelPath();
    }

    /**
     * Authorizes user and shows admin page.
     */
    public function adminPage() {
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            header('WWW-Authenticate: Basic realm="Admin page"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Authorization failed';
            exit;
        }

        $userName = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        // Checking user data
        $userData = $this->model->getUserData($userName);
        if (!isset($userData) || password_verify($password, $userData['password']) === false) {
            header('WWW-Authenticate: Basic realm="Admin page"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Incorrect login data';
            exit;
        }

        // Showing admin page
        $parameters = array_key_exists('parameters', $this->route) ? $this->route['parameters'] : [];
	    $bookData = $this->booksModel->getBooks($parameters);
        $this->view->render($bookData);
    }

    /**
     * Add new book and its image.
     */
	public function addBook() {
        $this->booksModel->addBook($_POST);

        $image = $_FILES['image'];
        $this->booksModel->uploadImage($image);

        header("Location: http://shpp.level3/admin");
	}

    /**
     * Deletes book.
     */
	public function deleteBook() {
		$this->booksModel->deleteBook($this->route['parameters']['id']);

        header("Location: http://shpp.level3/admin");
	}
}
