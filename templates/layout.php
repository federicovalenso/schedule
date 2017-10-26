<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title><?= $title ?></title>
        <link href="css/style.css" rel="stylesheet">
        <link href="css/normalize.min.css" rel="stylesheet">
    </head>
    <body>
        <main>
            <nav class="nav">
                <ul class="nav__list container">
                    <li class="nav__item">
                        <a href="docs.php">Врачи</a>
                    </li>
                    <li class="nav__item">
                        <a href="posts.php">Должности</a>
                    </li>                    
                    <li class="nav__item">
                        <a href="index.php">Расписание</a>
                    </li>
                </ul>
            </nav>
            <?= $content ?>
        </main>
    </body>
    <script src="js/functions.js"></script>
</html>