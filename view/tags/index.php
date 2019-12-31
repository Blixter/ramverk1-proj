<?php

namespace Anax\View;

use Blixter\Gravatar\Gravatar;

$gravatar = new Gravatar;
$questionURL = url("question/create");

?>

<a href="<?=$questionURL?>"><p>Ask a question</p></a>

<h1>All questions with tag</h1>

<?php foreach ($questions as $question): ?>
    <div class="container border-bottom">
        <div class="rows">
            <div class="col-sm-2 p-2">
                <div>
                    Points: <?=$question["question"]->points?>
                </div>
                <div>
                    Answers: <?=$question["answerCount"]?>
                </div>
            </div>
            <div class="col-sm-8 p-2">
                <a href="<?=url("question/post/" . $question["question"]->id)?>">
                    <p style="margin: 0;"><?=$question["question"]->title?></p>
                </a>
                <div><?=substr($question["question"]->question, 0, 80)?>...</div>
                <div class="container">
                    <div class="rows justify-content-start text-light a-inherit">
                        <?php foreach ($question["tags"] as $tag): ?>
                            <a role="button" class="col- mr-2 btn btn-secondary btn-sm" href="<?=url("tags/get/" . $tag->tagId)?>"><?=$tag->tagName?></a>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <small><?=date('d F Y, h:i', $question["question"]->created)?></small>
                    <div><img src="<?=$gravatar->getGravatar($question["question"]->email, 50)?>"></div>
                    <p style="margin: 0;"><a href="<?=url("question/user/" . $question["question"]->userId)?>"><?=$question["question"]->username?></a></p>
            </div>
        </div>
    </div>
    <?php endforeach;?>