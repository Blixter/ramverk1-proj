<?php
/**
 * Supply the basis for the navbar as an array.
 */
return [
    // Use for styling the menu
    "wrapper" => null,
    "class" => "my-navbar rm-default rm-desktop a-inherit",

    // Here comes the menu items
    "items" => [
        [
            "text" => "Hem",
            "url" => "",
            "title" => "Första sidan, börja här.",
        ],
        [
            "text" => "Om",
            "url" => "om",
            "title" => "Om denna webbplats.",
        ],
        [
            "text" => "Questions",
            "url" => "question",
            "title" => "All questions",
        ],
        [
            "text" => "Login",
            "url" => "user/login",
            "title" => "Login",
        ],
        [
            "text" => "Register",
            "url" => "user/create",
            "title" => "Register",
        ],
    ],
];
