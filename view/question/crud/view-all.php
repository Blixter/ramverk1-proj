<?php

namespace Anax\View;

/**
 * View to display all books.
 */
// Show all incoming variables/functions
//var_dump(get_defined_functions());
//echo showEnvironment(get_defined_vars());

// Gather incoming variables and use default values if not set
$items = isset($items) ? $items : null;

// Create urls for navigation
$urlToCreate = url("question/create");
$urlToDelete = url("question/delete");

?><h1>View all items</h1>

<p>
    <a href="<?=$urlToCreate?>">Create</a> |
    <a href="<?=$urlToDelete?>">Delete</a>
</p>

<?php if (!$items): ?>
    <p>There are no items to show.</p>
<?php
return;
endif;
?>

<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Question</th>
        <th>userId</th>
        <th>Created</th>
        <th>Points</th>
    </tr>
    <?php foreach ($items as $item): ?>
    <tr>
        <td>
            <a href="<?=url("question/update/{$item->id}");?>"><?=$item->id?></a>
        </td>
        <td><?=$item->title?></td>
        <td><?=$item->question?></td>
        <td><?=$item->userId?></td>
        <td><?=date('d F Y, h:i:s', $item->created)?></td>
        <td><?=$item->points?></td>
    </tr>
    <?php endforeach;?>
</table>
