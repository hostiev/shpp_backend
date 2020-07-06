<?php
$pagesNumber = ceil($booksCount / $offset);
?>

<div class="container">
    <div class="row">
        <div class="col">
            <a id="logout" href="" class="btn btn-primary btn-success float-right" role="button" aria-pressed="true" onclick="logout()">Logout</a>
        </div>
    </div>
    <div class="row">
        <div class="col-8">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Authors</th>
                    <th scope="col">Year</th>
                    <th scope="col">Action</th>
                    <th scope="col">Clicks</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    foreach ($books as $book) :
                ?>
                <tr>
                    <th scope="row"> <?php echo $book['name'] ?> </th>
                    <td> <?php echo $book['author'] ?> </td>
                    <td> <?php echo $book['year'] ?> </td>
                    <td><a href="<?php echo 'http://shpp.level3/admin/deleteBook?id=' . $book['id'] ?>">Delete</a> </td>
                    <td> <?php echo $book['clicks'] ?> </td>
                </tr>
                <?php
                    endforeach;
                ?>
                </tbody>
            </table>


        </div>
        <div class="col">
            <form action="http://shpp.level3/admin/addBook" enctype="multipart/form-data" method="post">
                <label>Add new book</label>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" name="title" placeholder="Book title">
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" name="author1" placeholder="Author 1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" name="year" placeholder="Year">
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" name="author2" placeholder="Author 2">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="file" class="custom-file-input" id="image" name="image">
                        <label class="custom-file-label" for="image">Choose image</label>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" name="author3" placeholder="Author 3">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <span id="thumbnail"></span>
                    </div>
                    <div class="form-group col-md-6">
                        <textarea class="form-control" name="about" rows="3" placeholder="About..."></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add book</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="center-block">
            <nav aria-label="Books navigation">
                <ul class="pagination">
                    <li class="page-item <?php echo $page > 1 ? '' : ' disabled'; ?>">
                        <a class="page-link" href="<?php echo 'http://shpp.level3/admin?page=' . ($page - 1); ?>" aria-disabled="<?php echo $page > 1 ? 'false' : 'true'; ?>">Previous</a>
                    </li>
                    <?php
                    for ($i = 1; $i <= $pagesNumber; $i++) {
                        if ($i == $page) {
                            echo '<li class="page-item active" aria-current="page">
                                <a class="page-link" href="http://shpp.level3/admin?page=' . $i . '">'. $i .' <span class="sr-only">(current)</span></a>
                              </li>';
                        } else {
                            echo '<li class="page-item"><a class="page-link" href="http://shpp.level3/admin?page=' . $i . '">'. $i .'</a></li>';
                        }
                    }
                    ?>
                    <li class="page-item <?php echo $page < $pagesNumber ? '' : ' disabled'; ?>">
                        <a class="page-link" href="<?php echo 'http://shpp.level3/admin?page=' . ($page + 1); ?>" aria-disabled="<?php echo $page < $pagesNumber ? 'false' : 'true'; ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<script>
    function logout() {
        jQuery.ajax({
            type: "GET",
            url: "http://shpp.level3/admin",
            async: false,
            username: "logmeout",
            password: "123456",
            headers: { "Authorization": "Basic xxx" }
        })
            .done(function(){
            })
            .fail(function(){
                window.location = "http://shpp.level3/";
            });

        return false;
    }

    function handleFileSelect(evt) {
        var file = evt.target.files; // FileList object
        var f = file[0];
        // Only process jpg image files
        if (!f.type.match('image/jpeg')) {
            alert("Not .jpg image!");
            return;
        }
        var reader = new FileReader();
        // Closure to capture the file information
        reader.onload = (function(theFile) {
            return function(e) {
                // Render thumbnail
                var span = document.createElement('span');
                span.innerHTML = ['<img class="img-thumbnail" id="thumbnail" title="', escape(theFile.name), '" src="', e.target.result, '" />'].join('');
                document.getElementById('thumbnail').replaceWith(span);
            };
        })(f);
        // Read in the image file as a data URL
        reader.readAsDataURL(f);
    }
    document.getElementById('image').addEventListener('change', handleFileSelect, false);
</script>