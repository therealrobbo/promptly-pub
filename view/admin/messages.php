<?php if ( !empty( $page_messages ) ) {
    foreach( $page_messages as $page_message ) { ?>
        <div class="alert alert-<?= $page_message['type'] ?>"><?= $page_message['text'] ?></div>
    <?php }
} ?>
