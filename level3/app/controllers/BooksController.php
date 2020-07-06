<?php

namespace app\controllers;

use app\core\Controller;

/**
 * Manages books pages actions.
 */
class BooksController extends Controller
{
    /**
     * Shows the page of one book.
     */
	public function showBook() {
	    // Increasing clicks counter if user clicked on "Want" button
        if (array_key_exists('parameters',  $this->route)) {
            if (array_key_exists('want', $this->route['parameters'])) {
                $this->model->increaseClicks($this->route['parameters']['want']);
                exit();
            }
        }

        $bookID = $this->route['path'][1];
		$bookData = $this->model->getBookById($bookID);
		$this->view->render($bookData);
	}

    /**
     * Shows books collection page.
     */
    public function showBooks() {
        $parameters = array_key_exists('parameters', $this->route) ? $this->route['parameters'] : [];
        $bookData = $this->model->getBooks($parameters);
        $this->view->render($bookData);
    }

    /**
     * Shows search page.
     */
    public function search() {
        $parameters = array_key_exists('parameters', $this->route) ? $this->route['parameters'] : [];
        $bookData = $this->model->searchBooks($parameters);
        $this->view->render($bookData);
    }
}
