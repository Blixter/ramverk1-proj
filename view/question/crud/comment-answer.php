<?php

namespace Anax\View;

?>

<h1>Comment answer</h1>
<div class="container border-bottom">
    <small><?=date('d F Y, h:i:s', $answer->created)?> by <a href="<?=url("user/view/{$answer->userId}")?>"><strong><?=$answer->username?></strong></a></small>
    <div class="markdown"><?=$answerParsed?></div>
</div>

<?php foreach ($comments as $comment): ?>
    <div class="">
            <div class="border-bottom pl-5 p-0 mt-1 mb-1">
                <small><?=date('d F Y, h:i:s', $comment->created)?> by <a href="<?=url("user/view/{$comment->userId}")?>"><strong><?=$comment->username?></strong></a></small>
                <?=$comment->commentParsed?>
            </div>
        </div>
<?php endforeach?>


<?=$form;?>



