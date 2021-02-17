<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Todos</title>

    <link rel="stylesheet" type="text/css" href="<?= $_static('css/app.css') ?>">
    <script type="text/javascript" src="<?= $_static('js/app.js') ?>" defer></script>
</head>

<body>
    <div id="app">
        <div class="header">
            <div class="header-title">todos</div>
        </div>
        <div class="main">
            <?= $_view('contents') ?>
        </div>
    </div>
</body>
</html>