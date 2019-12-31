<?php

namespace Anax\View;

?>

<h1>All Tags</h1>

<div class="container">
    <div class="rows justify-content-start text-light a-inherit">
    <?php foreach ($tags as $tag): ?>
        <a role="button" class="col- mr-2 btn btn-secondary btn-sm" href="<?=url("tags/get/" . $tag->id)?>"><?=$tag->tagName?></a>
    <?php endforeach;?>
    </div>
</div>



