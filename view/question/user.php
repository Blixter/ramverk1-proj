<?php

namespace Anax\View;

use Blixter\Gravatar\Gravatar;
use Blixter\User\User;

$gravatar = new Gravatar();

$user = new User();
$user->setDb($this->di->get("dbqb"));

?><h1>User questions</h1>


<?php foreach ($questions as $item): ?>
    <?php $currentUser = $user->find("id", $item["question"]->userId);?>
    <div>
    <a href="<?=url("question/post/" . $item["question"]->id)?>"><h2><?=$item["question"]->title?> - <?=$item["question"]->points?> points</h2></a>
        <p><?=$item["question"]->question?></p>
        <p><a href="<?=url("question/user/" . $currentUser->id)?>"><?=$currentUser->username?></a></p>
        <div><img src="<?=$gravatar->getGravatar($currentUser->email, 50)?>"></div>
        <?=date('d F Y, h:i:s', $item["question"]->created)?>
        <?php foreach ($item["tags"] as $tag): ?>
            <div><a href="<?=url("tags/get/" . $tag->id)?>"><?=$tag->tagName?></a></div>
        <?php endforeach;?>
    </div>
<?php endforeach;?>




