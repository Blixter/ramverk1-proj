<?php

namespace Anax\View;

use Blixter\Gravatar\Gravatar;

$gravatar = new Gravatar();

// $session = $di->session->delete("login");
$userId = $di->session->get("login") ?? null;
$disabled = $userId ? null : "disabled";
$hidden = $userId ? null : "visibility: hidden;";
$notLoggedInMessage = $userId ? null : "Log in if you want to vote, answer or comment the post!";
?>


<!-- <form action=<?=url("iptojson")?> method="get" class="form">
        <button type="submit" class="btn btn-success mb-2" name="ip" value="8.8.8.8">Validerar</button>
        <button type="submit" class="btn btn-danger mb-2" name="ip" value="201.923.1.123">Validerar inte</button>
</form -->

<h1><?=$question->title;?></h1>
<div class="container border-bottom">
    <div class="rows">
        <div class="col-sm-1 p-2 text-center">
            <div class="d-flex flex-column mb-3 text-black-50">
                <div class="p-2">
                    <form action=<?=url("question/vote")?> method="get">
                        <input hidden name="userId" value="<?=$userId?>">
                        <input hidden name="questionId" value="<?=$question->id?>">
                        <button type="submit" name="vote" value="up" class="fabutton" <?=$disabled?>>
                            <i class="fas fa-lg fa-arrow-up"></i>
                        </button>
                    </form>
                </div>
                <div class="p-2 p-0">
                    <p class="h4"><b><?=$question->points?></b>
                </div>
                <div class="p-2">
                    <form action=<?=url("question/vote")?> method="get">
                        <input hidden name="userId" value="<?=$userId?>">
                        <input hidden name="questionId" value="<?=$question->id?>">
                        <button type="submit" name="vote" value="down" class="fabutton" <?=$disabled?>>
                            <i class="fas fa-lg fa-arrow-down"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-11 p-2">
            <div class="markdown"><?=$questionParsed?></div>
            <div class="container">
                <div class="rows justify-content-start text-light a-inherit">
                    <?php foreach ($tags as $tag): ?>
                        <a role="button" class="col- mr-2 btn btn-secondary btn-sm" href="<?=url("tags/get/" . $tag->tagId)?>"><?=$tag->tagName?></a>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <small><?=date('d F Y, h:i', $question->created)?></small>
            <div><img src="<?=$gravatar->getGravatar($question->email, 50)?>"></div>
            <p style="margin: 0;"><a href="<?=url("question/user/" . $question->userId)?>"><?=$question->username?></a></p>
    </div>
    </div>
        <div class="a-inherit text-light" style="<?=$hidden?>">
            <a class="btn btn-primary" href="<?=url("answer/question/" . $question->id)?>">Answer <i class="fas fa-share fa-lg fa-flip-horizontal"></i></a>
            <a class="btn btn-primary" href="<?=url("question/comment/" . $question->id)?>">Comment <i class="fas fa-comment fa-lg"></i></a>
        </div>


<!-- <div class="">
    <p><?=$answers?></p>
</div> -->

<!-- <?php var_dump($answers);?> -->

<?php foreach ($comments as $comment): ?>
        <div class="" style="padding: 0.5rem; margin: 0.5rem;">
            <div class="" style="border-left: 5px solid gray; padding: 0.5rem;">
                <p>
                    Comment - Posted <small><?=date('d F Y, h:i:s', $comment->created)?></small> by <a href="<?=url("user/view/{$comment->userId}")?>"><strong><?=$comment->username?></strong></a>
                </p>
                <?=$comment->commentParsed?>
            </div>
        </div>

    <?php endforeach?>

<?php foreach ($answers as $answer): ?>

        <div class="" style="border: 2px solid gray; padding: 0.5rem; margin: 0.5rem;">
    <p>Answer - Posted <?=date('d F Y, h:i:s', $answer->created)?> By <?=$answer->username?> <p>
    <p><?=$answer->answerParsed?></p>
    <p>
    <a class="btn btn-primary" href="<?=url("answer/comment/" . $answer->id)?>">Comment <i class="fas fa-comment fa-lg"></i></a>
</p>
</div>
<?php foreach ($answer->comments as $comment): ?>
    <div class="" style="padding: 0.5rem; margin: 0.5rem;">
                <div class="" style="border-left: 5px solid gray; padding: 0.5rem;">
                    <p>
                        Comment - Posted <small><?=date('d F Y, h:i:s', $comment->created)?></small> by <a href="<?=url("user/view/{$comment->userId}")?>"><strong><?=$comment->username?></strong></a>
                    </p>
                    <?=$comment->commentParsed?>
                </div>
            </div>
    <?php endforeach?>

<?php endforeach?>

<p><?=$notLoggedInMessage?></p>