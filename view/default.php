<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= ( !empty( $title ) ? $title  : $site_title ) ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $site_name ?> - <?= ( !empty( $seo_descr ) ? $seo_descr : "Wake up and write. Daily prompts for writers" ) ?>" >
    <link rel="shortcut icon" type="image/ico" href="/assets/img/coffee-icon.png">

    <!-- OG tags -->
    <?php if ( !empty( $og_meta ) ) {
        foreach( $og_meta as $og_meta_info ) { ?><meta property="<?= $og_meta_info['property'] ?>" content="<?= $og_meta_info['content'] ?>" />
    <?php } } ?>
    <!-- END OG tags -->

    <?php if ( !empty( $canonical_url ) ) { ?><link rel="canonical" href="<?= $canonical_url ?>" /><?php } ?>

    <?= $this->asset_retrieve( REQ_ASSET_CSS ); ?>
    <?= $this->asset_retrieve( REQ_ASSET_JS_GLOBAL ); ?>
    <?= $this->asset_retrieve( REQ_ASSET_JS ); ?>

    <?php if ( !empty( $partner_codes_100 ) ) {
        foreach( $partner_codes_100 as $partner_code_rec ) { ?>
            <?= $partner_code_rec['pc_code'] ?>
    <?php } } ?>

</head>
<body class="word-coffee">
    <?php if ( !empty( $partner_codes_200 ) ) {
        foreach( $partner_codes_200 as $partner_code_rec ) { ?>
            <?= $partner_code_rec['pc_code'] ?>
    <?php } } ?>


    <?php include( 'default_head.php' ); ?>

    <?php include( $template ); ?>

    <?php include( 'default_foot.php' ); ?>

    <?php if ( !empty( $partner_codes_300 ) ) {
        foreach( $partner_codes_300 as $partner_code_rec ) { ?>
            <?= $partner_code_rec['pc_code'] ?>
    <?php } } ?>
</body>
<?php if ( !empty( $partner_codes_400 ) ) {
    foreach( $partner_codes_400 as $partner_code_rec ) { ?>
        <?= $partner_code_rec['pc_code'] ?>
    <?php } } ?>
</html>