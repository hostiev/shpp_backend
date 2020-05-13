<!-- <!DOCTYPE html> -->
  <head>
    <meta charset="utf-8">
    <title>task 6 - counter.php</title>
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>
  
  <body>
    <div class="text-center cover-container d-flex h-100 flex-column">
      <header class="mb-auto inner"></header>
      <main  class="inner cover">
        <h1 class="cover-heading"> Visits: 
          <?php 
            $counterValue = file_get_contents('counterValue.txt');
            echo $counterValue++;
            file_put_contents('counterValue.txt', $counterValue);
          ?>
          <br/></h1>
        <a href="index.html" class="lead btn btn-lg btn-secondary">Back to main</a>
      </main>
      <footer class="mt-auto inner"/>
    </div>
  </body>
<!-- </html> -->
