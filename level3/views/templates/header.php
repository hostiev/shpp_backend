<section id="header" class="header-wrapper">
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="col-xs-5 col-sm-2 col-md-2 col-lg-2">
                <div class="logo"><a href="http://localhost" class="navbar-brand"><span class="sh">Ш</span><span class="plus">++</span></a></div>
            </div>
            <div class="col-xs-12 col-sm-7 col-md-8 col-lg-8">
                <div class="main-menu">
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <form class="navbar-form navbar-right">
                            <div class="form-group">
                                <input id="search" type="text" placeholder="Найти книгу" class="form-control">
                                <div class="loader"><img src="./book-page_files/loading.gif"></div>
                                <div id="list" size="" class="bAutoComplete mSearchAutoComplete"></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-2 col-sm-3 col-md-2 col-lg-2 hidden-xs">
                <div class="social"><a href="https://www.facebook.com/shpp.kr/" target="_blank"><span class="fa-stack fa-sm"><i class="fa fa-facebook fa-stack-1x"></i></span></a><a href="http://programming.kr.ua/ru/courses#faq" target="_blank"><span class="fa-stack fa-sm"><i class="fa fa-book fa-stack-1x"></i></span></a></div>
            </div>
        </div>
    </nav>
    <script>
        $("#search").bind("keypress", function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                var text = htmlspecialchars($('#search').val());
                if (text.length > 0) {
                    text = text.replace(/(^\s+|\s+$)/g, '');
                    var textEncode = encodeURIComponent(text); // shielding request
                    window.location = 'http://' + window.location.host + '/search?search=' + textEncode + '';
                }
            }
        })
    </script>
</section>