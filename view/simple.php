<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= ( !empty( $title ) ? $title  : $site_title ) ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=Edge">

    <?= $this->asset_retrieve( REQ_ASSET_CSS ); ?>
    <?= $this->asset_retrieve( REQ_ASSET_JS_GLOBAL ); ?>
    <?= $this->asset_retrieve( REQ_ASSET_JS ); ?>

</head>
<body class="facs">
    <?php include( $template ); ?>
</body>