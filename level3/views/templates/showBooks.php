<?php
    $pagesNumber = ceil($booksCount / $offset);
?>

<section id="main" class="main-wrapper">
<div class="container">
    <div id="content" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <?php
            foreach ($books as $book) :
        ?>
        <div data-book-id="<?php echo $book['id'] ?>" class="book_item col-xs-6 col-sm-3 col-md-2 col-lg-2">
            <div class="book">
                <a href="http://shpp.level3/books/<?php echo $book['id'] ?>"><img src="../assets/books-page_files/<?php echo $book['id'] ?>.jpg" alt="<?php echo $book['name'] ?>">
                    <div data-title="<?php echo $book['name'] ?>" class="blockI" style="height: 46px;">
                        <div data-book-title="<?php echo $book['name'] ?>" class="title size_text"><?php echo $book['name'] ?></div>
                        <div data-book-author="<?php echo $book['author'] ?>" class="author"><?php echo $book['author'] ?></div>
                    </div>
                </a>
                <a href="http://shpp.level3/books/<?php echo $book['id'] ?>">
                    <button type="button" class="details btn btn-success">Читать</button>
                </a>
            </div>
        </div>
        <?php
            endforeach;
        ?>
    </div>
</div>
<div class="text-center">
    <nav aria-label="Books navigation">
        <ul class="pagination">
            <li class="page-item <?php echo $page > 1 ? '' : ' disabled'; ?>">
                <a class="page-link" href="<?php echo 'http://shpp.level3/?offset=' . $offset . '&page=' . ($page - 1); ?>" aria-disabled="<?php echo $page > 1 ? 'false' : 'true'; ?>">Previous</a>
            </li>
            <?php
                for ($i = 1; $i <= $pagesNumber; $i++) {
                    if ($i == $page) {
                        echo '<li class="page-item active" aria-current="page">
                                <a class="page-link" href="http://shpp.level3/?offset=' . $offset . '&page=' . $i . '">'. $i .' <span class="sr-only">(current)</span></a>
                              </li>';
                    } else {
                        echo '<li class="page-item"><a class="page-link" href="http://shpp.level3/?offset=' . $offset . '&page=' . $i . '">'. $i .'</a></li>';
                    }
                }
            ?>
            <li class="page-item <?php echo $page < $pagesNumber ? '' : ' disabled'; ?>">
                <a class="page-link" href="<?php echo 'http://shpp.level3/?offset=' . $offset . '&page=' . ($page + 1); ?>" aria-disabled="<?php echo $page < $pagesNumber ? 'false' : 'true'; ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>