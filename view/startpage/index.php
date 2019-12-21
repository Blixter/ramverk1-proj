<?php

namespace Anax\View;

use Blixter\Gravatar\Gravatar;

$gravatar = new Gravatar;

?><h1>View latest questions</h1>

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
                <div><?=substr($question["questionParsed"], 0, 80)?>...</div>
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

<div class="container">
    <div class="rows">
        <div class="col">
            <h1>Most popular tags</h1>
            <?php foreach ($popularTags as $tag): ?>

                <div class="container a-inherit text-light mt-4">
                    <a role="button" class="col- p-2 btn-secondary btn-sm a-inherit" href="<?=url("tags/get/" . $tag->tagId)?>"><?=$tag->tagName?></a> <span class="text-dark"><b><?=$tag->count;?> Questions</b></span>
                </div>
            <!-- <div><a href="<?=url("tags/get/" . $tag->tagId)?>"><?=$tag->tagName?></a> Amount: <?=$tag->count;?></div> -->

            <?php endforeach;?>
        </div>

        <div class="col">
            <h1>Most active users</h1>

            <?php foreach ($mostActiveUser as $user): ?>

                <div class="container mt-4">
                    <div><img src="<?=$gravatar->getGravatar($question["question"]->email, 60)?>"></div>
                    <div class="col-"><a href="<?=url("question/user/" . $user->id)?>"><?=$user->username?></a></div>
                    <div><b>Activepoints: <?=$user->activepoints;?></b></div>
                </div>

            <?php endforeach;?>
        </div>