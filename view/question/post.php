<?php

namespace Anax\View;

use Blixter\Answer\UserVoteOnAnswer;
use Blixter\Gravatar\Gravatar;
use Blixter\Question\UserVoteOnQuestion;

$gravatar = new Gravatar();
$userVoteOnQ = new UserVoteOnQuestion();
$userVoteOnQ->setDb($di->get("dbqb"));
$userVoteOnA = new UserVoteOnAnswer();
$userVoteOnA->setDb($di->get("dbqb"));

$loginURL = url("user/login");

$userId = $di->session->get("login") ?? null;
$disabled = $userId ? null : "disabled";
$hidden = $userId ? null : "visibility: hidden;";
$notLoggedInMessage = $userId ? null : "<a href='$loginURL'>Log in</a> if you want to vote, answer or comment the post!";
$voteTextColorUp = null;
$voteTextColorDown = null;
$answerVoteTextColorUp = null;
$answerVoteTextColorDown = null;

if ($userId) {
    $questionVote = $userVoteOnQ->getVote($question->id, $userId);
    if ($questionVote == "up") {
        $voteTextColorUp = "text-primary";
    } else if ($questionVote == "down") {
        $voteTextColorDown = "text-primary";
    }
}

?>

<h1><?=$question->title;?></h1>
<div class="container border-bottom">
    <div class="rows">
        <div class="col-1 p-2 text-center">
            <div class="d-flex flex-column mb-3 text-black-50">
                <div class="p-2 <?=$voteTextColorUp?>">
                    <form action=<?=url("question/vote")?> method="get">
                        <input hidden name="userId" value="<?=$userId?>">
                        <input hidden name="questionId" value="<?=$question->id?>">
                        <button type="submit" name="vote" value="up" class="fabutton " <?=$disabled?>>
                            <i class="fas fa-lg fa-arrow-up"></i>
                        </button>
                    </form>
                </div>
                <div class="p-2 p-0">
                    <p class="h4"><b><?=$question->points?></b>
                </div>
                <div class="p-2 <?=$voteTextColorDown?>">
                    <form action=<?=url("question/vote")?> method="get">
                        <input hidden name="userId" value="<?=$userId?>">
                        <input hidden name="questionId" value="<?=$question->id?>">
                        <button type="submit" name="vote" value="down" class="fabutton " <?=$disabled?>>
                            <i class="fas fa-lg fa-arrow-down"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-11 p-2">
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
        <div class="container">
            <div class="rows justify-content-center">
                <div class="col-sm-7">
                <div class="a-inherit text-light mt-4" style="<?=$hidden?>">
            <a class="btn btn-secondary btn-sm" href="<?=url("answer/question/" . $question->id)?>">Answer <i class="fas fa-share fa-lg fa-flip-horizontal"></i></a>
            <a class="btn btn-secondary btn-sm" href="<?=url("question/comment/" . $question->id)?>">Comment <i class="fas fa-comment fa-lg"></i></a>
        </div>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="pt-4">
                        <small><?=date('d/m Y', $question->created)?></small>
                        <small><?=date('H:i', $question->created)?></small>
                    </div>
                </div>
                <div class="col-sm-2 text-center">
                    <div><img src="<?=$gravatar->getGravatar($question->email, 40)?>"></div>
                    <p style="margin: 0;"><a href="<?=url("user/view/" . $question->userId)?>"><?=$question->username?></a></p>
                </div>
            </div>
        </div>
    </div>


<?php foreach ($comments as $comment): ?>
    <div class="">
        <div class="border-bottom pl-5 p-0 mt-1 mb-1">
                <small><?=date('d F Y, H:i:s', $comment->created)?> by <a href="<?=url("user/view/{$comment->userId}")?>"><strong><?=$comment->username?></strong></a></small>
                <?=$comment->commentParsed?>
            </div>
        </div>

    <?php endforeach?>

<?php if ($answerCount > 0): ?>
    <h1><?=$answerCount?> Answers</h1>
    <div class="container">
        <div class="rows justify-content-end">
            <div class="col-sm-3">
                Sort on:
                <a href="<?=url("question/post/" . $question->id . "?sort=Created")?>">Date</a>
                |
                <a href="<?=url("question/post/" . $question->id . "?sort=Points")?>">Points</a>
        </div>
    </div>
<?php endif;?>


<?php foreach ($answers as $answer): ?>
<?php
$answerVoteTextColorUp = null;
$answerVoteTextColorDown = null;
$userVoteOnA = new UserVoteOnAnswer();
$userVoteOnA->setDb($di->get("dbqb"));
if ($userId) {
    $answerVote = $userVoteOnA->getVote($answer->id, $userId);
    if ($answerVote == "up") {
        $answerVoteTextColorUp = "text-primary";
    } else if ($answerVote == "down") {
        $answerVoteTextColorDown = "text-primary";
    }
}
?>
<div class="container border-bottom">
    <div class="rows">
        <div class="col-1 p-2 text-center">
            <div class="d-flex flex-column mb-3 text-black-50">
                <div class="p-2 <?=$answerVoteTextColorUp?>">
                    <form action=<?=url("answer/vote")?> method="get">
                        <input hidden name="userId" value="<?=$userId?>">
                        <input hidden name="questionId" value="<?=$question->id?>">
                        <input hidden name="answerId" value="<?=$answer->id?>">
                        <button type="submit" name="vote" value="up" class="fabutton " <?=$disabled?>>
                            <i class="fas fa-lg fa-arrow-up"></i>
                        </button>
                    </form>
                </div>
                <div class="p-2 p-0">
                    <p class="h4"><b><?=$answer->points?></b>
                </div>
                <div class="p-2 <?=$answerVoteTextColorDown?>">
                    <form action=<?=url("answer/vote")?> method="get">
                        <input hidden name="userId" value="<?=$userId?>">
                        <input hidden name="questionId" value="<?=$question->id?>">
                        <input hidden name="answerId" value="<?=$answer->id?>">
                        <button type="submit" name="vote" value="down" class="fabutton " <?=$disabled?>>
                            <i class="fas fa-lg fa-arrow-down"></i>
                        </button>
                    </form>
                </div>
                <div class="p-2 p-0">
                    <?php if ($answer->accepted == 1): ?>
                        <i title="This is an accepted answer" class="fas fa-lg fa-check" style="color: green;"></i>
                    <?php elseif ($userId and $userId == $question->userId): ?>
                        <form action=<?=url("answer/accept")?> method="get">
                            <input hidden name="userId" value="<?=$userId?>">
                            <input hidden name="questionId" value="<?=$question->id?>">
                            <input hidden name="answerId" value="<?=$answer->id?>">
                            <button type="submit" name="vote" value="down" class="fabutton " <?=$disabled?>>
                                <i title="Mark as accepted answer" class="fas fa-lg fa-check"></i>
                            </button>
                        </form>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <div class="col-11 p-2">
            <div class="markdown"><?=$answer->answerParsed?></div>
        </div>
        <div class="container">
            <div class="rows justify-content-center">
                <div class="col-sm-7">
                <div class="a-inherit text-light mt-4" style="<?=$hidden?>">
            <a class="btn btn-secondary btn-sm" href="<?=url("answer/comment/" . $answer->id)?>">Comment <i class="fas fa-comment fa-lg"></i></a>
        </div>
                </div>
                <div class="col-sm-3 text-center">
                    <div class="pt-4">
                        <small><?=date('d/m Y', $answer->created)?></small>
                        <small><?=date('H:i', $answer->created)?></small>
                    </div>
                </div>
                <div class="col-sm-2 text-center">
                    <div><img src="<?=$gravatar->getGravatar($answer->email, 40)?>"></div>
                    <p style="margin: 0;"><a href="<?=url("user/view/" . $answer->userId)?>"><?=$answer->username?></a></p>
                </div>
            </div>
        </div>
    </div>

</div>
<?php foreach ($answer->comments as $comment): ?>
    <div class="">
            <div class="border-bottom pl-5 p-0 mt-1 mb-1">
                <small><?=date('d F Y, H:i:s', $comment->created)?> by <a href="<?=url("user/view/{$comment->userId}")?>"><strong><?=$comment->username?></strong></a></small>
                <?=$comment->commentParsed?>
            </div>
        </div>
    <?php endforeach?>

<?php endforeach?>

<p><?=$notLoggedInMessage?></p>