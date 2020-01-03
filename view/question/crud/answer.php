<?php

namespace Anax\View;

?>

<h1>Answer question</h1>



<div class="">
    <small><?=date('d F Y, H:i:s', $question->created)?> by <a href="<?=url("user/view/{$question->userId}")?>"><strong><?=$question->username?></strong></a></small>
    <div class="markdown"><?=$questionParsed?></div>
</div>

<?=$form;?>
