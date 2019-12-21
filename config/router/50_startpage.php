<?php

/**
 * Mount the controller onto a mountpoint.
 */
return [
    "routes" => [
        [
            "info" => "Startpage",
            "mount" => "/",
            "handler" => "\Blixter\Startpage\StartpageController",
        ],
    ],
];
