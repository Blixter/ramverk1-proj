<?php

/**
 * Mount the controller onto a mountpoint.
 */
return [
    "routes" => [
        [
            "info" => "Tags controller.",
            "mount" => "tags",
            "handler" => "\Blixter\Tags\TagsController",
        ],
    ],
];
