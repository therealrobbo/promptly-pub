<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= ( !empty( $title ) ? $title  : $site_name ) ?></title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="nofollow" />

    <link rel="shortcut icon" type="image/ico" href="/assets/img/coffee-icon.png">

    <?= $this->asset_retrieve( REQ_ASSET_CSS ); ?>
    <?= $this->asset_retrieve( REQ_ASSET_JS_GLOBAL ); ?>
    <?= $this->asset_retrieve( REQ_ASSET_JS ); ?>

</head>
<body class="super_admin">

    <?php include( 'default_head.php' ); ?>

    <?php include( $template ); ?>

    <?php include( 'default_foot.php' ); ?>
</body>