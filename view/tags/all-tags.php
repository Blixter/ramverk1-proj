<?php

namespace Anax\View;

?>


<?php foreach ($tags as $tag): ?>
    <div><a href="<?=url("tags/get/" . $tag->id)?>"><?=$tag->tagName?></a></div>
<?php endforeach;?>




