<?php
/**
 * Supply the basis for the navbar as an array.
 */
global $di;

$session = $di->get("session");
if ($session->has("login")) {
    return [
        // Use for styling the menu
        "id" => "rm-menu",
        "wrapper" => null,
        "class" => "rm-default rm-mobile a-inherit",

        // Here comes the menu items
        "items" => [
            [
                "text" => "Home",
                "url" => "",
                "title" => "Home page",
            ],
            [
                "text" => "About",
                "url" => "about",
                "title" => "About the site",
            ],
            [
                "text" => "Questions",
                "url" => "question",
                "title" => "All questions",
            ],
            [
                "text" => "Tags",
                "url" => "tags",
                "title" => "All tags",
            ],
            [
                "text" => "Profile",
                "url" => "user/profile",
                "title" => "User profile",
            ],
            [
                "text" => "Logout",
                "url" => "user/logout",
                "title" => "Logout",
            ],
        ],
    ];
} else {
    return [
        // Use for styling the menu
        "id" => "rm-menu",
        "wrapper" => null,
        "class" => "rm-default rm-mobile a-inherit",

        // Here comes the menu items
        "items" => [
            [
                "text" => "Home",
                "url" => "",
                "title" => "Home page",
            ],
            [
                "text" => "About",
                "url" => "about",
                "title" => "About the site",
            ],
            [
                "text" => "Questions",
                "url" => "question",
                "title" => "All questions",
            ],
            [
                "text" => "Tags",
                "url" => "tags",
                "title" => "All tags",
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
}
