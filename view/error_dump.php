<!DOCTYPE html>
<html lang=\"en\">
<head>
    <title><?= ( !empty( $title ) ? $title . " - " : "" ) ?>Phantom Zone</title>

    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        .error_message {
            width: 500px;
            margin: 100px auto 0px;
            padding: 40px;
            color: #A00;
            font-family: verdana, arial, helvetica, sans-serif;
            border: 1px solid #A00;
        }
    </style>
</head>
<body>
    <div class="error_message">
        <h2>FATAL ERROR!</h2>
        <?= $message ?>
    </div>
</body>
</html>
