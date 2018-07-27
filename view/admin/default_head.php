<div class="container">
    <div class="site-header">
        <header role="banner">
            <div class="inner">

                <div id="logo-text"><a title="Return to the <?= $site_name ?> Admin homepage" href="<?= $admin_url ?>"><?= $site_name ?></a></div>

            </div><!-- /inner -->
        </header>
    </div>

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/"><?= $app_name ?></a>
            </div>

            <?php if ( $admin_is_logged_in ) { ?>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="<?= $admin_url ?>/prompt"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Writing Prompts <span class="sr-only">(current)</span></a></li>
                    <li><a href="<?= $admin_url ?>/user_admin"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Users <span class="sr-only">(current)</span></a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> Tools<span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= $admin_url ?>/partner_code"><span class="glyphicon glyphicon-qrcode" aria-hidden="true"></span> Partner Codes<span class="sr-only">(current)</span></a></li>
                            <li><a href="<?= $admin_url ?>/log_viewer"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Logs <span class="sr-only">(current)</span></a></li>
                            <li><a href="<?= $admin_url ?>/sysinfo"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> System Info<span class="sr-only">(current)</span></a></li>
                        </ul>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-heart" aria-hidden="true"></span> Hello, <?= $user_display_name ?><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?= $admin_url ?>/logout"><span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
            <?php } ?>
        </div><!-- /.container-fluid -->
    </nav>

    <div class="inner-container">
        <div class="content">
