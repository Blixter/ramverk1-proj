<?php
namespace Anax\View;

use Blixter\Comment\Comment;
use Blixter\Gravatar\Gravatar;
/**
 * Displays user posts in tables.
 */
$gravatar = new Gravatar;
$com = new Comment;
$com->setDb($this->di->get("dbqb"));
?>
<img src="<?=$gravatar->getGravatar($user->email)?>" alt="Gravatar">
<div>Reputation: <?=$user->reputation?></div>
<div>Total votes: <?=$user->votes?></div>
<h1 class="text-center">Overview - <?=$user->username?></h1>
<h2>Questions</h2>
<table class="table">
    <thead class="thead-light">
        <tr>
            <th scope="col">Question</th>
            <th scope="col">Date</th>
            <th scope="col">Points</th>
            <th scope="col">Read</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($questions as $question): ?>
        <tr>
            <td><?=mb_substr(($question->question), 0, 30) . " ..."?></td>
            <td><?=date('d F Y, h:i:s', $question->created)?></td>
            <td><?=$question->points?></td>
            <td><a href="<?=url("question/post/$question->id")?>">Go to post</a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<h2>Answers</h2>
<table class="table">
    <thead class="thead-light">
        <tr>
            <th scope="col">Answer</th>
            <th scope="col">Date</th>
            <th scope="col">Points</th>
            <th scope="col">Read</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($answers as $answer): ?>
        <tr>
            <td><?=mb_substr(($answer->answer), 0, 30) . " ..."?></td>
            <td><?=date('d F Y, h:i:s', $answer->created)?></td>
            <td><?=$answer->points?></td>
            <td><a href="<?=url("question/post/$answer->questionId")?>">Go to post</a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>

<h2>Comments</h2>
<table class="table">
    <thead class="thead-light">
        <tr>
            <th scope="col">Comment</th>
            <th scope="col">Date</th>
            <th scope="col">Type</th>
            <th scope="col">Read</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($comments as $comment): ?>
        <?php $currId = $comment->id;
$postId = $com->getQuestionIdForComment($currId);?>

        <tr>
            <td><?=mb_substr(($comment->comment), 0, 30) . " ..."?></td>
            <td><?=date('d F Y, h:i:s', $comment->created)?></td>
            <td><?=$comment->type?></td>
            <td><a href="<?=url("question/post/$postId")?>">Go to post</a></td>
        </tr>
    <?php endforeach;?>
    </tbody>
</table>