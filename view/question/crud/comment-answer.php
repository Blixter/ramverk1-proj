<?php

namespace Anax\View;

// var_dump($question->id);
// var_dump($question->title);
// var_dump($question->question);
// var_dump($question->username);
// var_dump($question->userId);
?>

<h1>Comment answer</h1>

<div class="card">
  <div class="card-body">
    <h5 class=""><?=$answer->username?></h5>
    <p class="card-text"><?=$answerParsed?></p>
  </div>
</div>

<?php foreach ($comments as $comment): ?>

<p>Posted <small><?=date('d F Y, h:i:s', $comment->created)?></small> by <?=$comment->username?></p>
<?=$comment->commentParsed?>

<?php endforeach?>

<?=$form;?>



