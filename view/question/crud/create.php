<?php

namespace Anax\View;

/**
 * View to create a question.
 */

// Gather incoming variables and use default values if not set
$items = isset($items) ? $items : null;
$loginURL = url("user/login");
$session = $di->get("session");

?><h1>Ask a Question</h1><?php
if ($session->has("login")) {
    echo $form;
} else {
    ?>
    <p><a href="<?=$loginURL?>">Login</a> to ask questions.</p><?php
}
