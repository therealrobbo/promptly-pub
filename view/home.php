<div class="row">
    <div id="prompt" data-prompt-id="<?= $prompt_id ?>" class="col-md-5 col-md-offset-3 prompt_full">
        <div id="prompt_buttons" class="btn-group" role="group" aria-label="...">
            <button id="prompt_shrink" type="button" class="btn btn-default btn-xs" title="shrink prompt"><span class="glyphicon glyphicon-resize-small" aria-hidden="true"></span></button>
            <button id="prompt_grow" type="button" class="btn btn-xs btn-default" title="restore prompt"><span class="glyphicon glyphicon-resize-full" aria-hidden="true"></span></button>
            <button id="prompt_hide" type="button" class="btn btn-xs btn-default"  title="hide prompt"><span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span></button>
        </div>
        <h3>Today's Prompt</h3>
        <div id="prompt_text"><?= $prompt_text ?></div>
    </div>
</div>

<div class="row">
    <div id="just_write">
        <textarea id="coffee_time"><?= $sample_text ?></textarea>
    </div>
</div>