<?php

namespace Blixter\User\HTMLForm;

use Anax\HTMLForm\FormModel;
use Blixter\User\User;
use Psr\Container\ContainerInterface;

/**
 * Example of FormModel implementation.
 */
class UserCreateForm extends FormModel
{
    /**
     * Constructor injects with DI container.
     *
     * @param Psr\Container\ContainerInterface $di a service container
     */
    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di);
        $this->form->create(
            [
                "id" => __CLASS__,
                "legend" => "Create user",
            ],
            [
                "username" => [
                    "type" => "text",
                    "class" => "form-control",
                    "validation" => ["not_empty"],
                ],

                "email" => [
                    "type" => "text",
                    "class" => "form-control",
                    "validation" => ["not_empty"],
                ],

                "password" => [
                    "type" => "password",
                    "class" => "form-control",
                    "validation" => ["not_empty"],
                ],

                "password-again" => [
                    "type" => "password",
                    "class" => "form-control",
                    "validation" => [
                        "match" => "password",
                        "not_empty",
                    ],
                ],

                "submit" => [
                    "type" => "submit",
                    "class" => "btn btn-primary",
                    "value" => "Create user",
                    "callback" => [$this, "callbackSubmit"],
                ],
            ]
        );
    }

    /**
     * Callback for submit-button which should return true if it could
     * carry out its work and false if something failed.
     *
     * @return boolean true if okey, false if something went wrong.
     */
    public function callbackSubmit()
    {
        // Get values from the submitted form
        $username = $this->form->value("username");
        $email = $this->form->value("email");
        $password = $this->form->value("password");
        $passwordAgain = $this->form->value("password-again");

        // Check password matches
        if ($password !== $passwordAgain) {
            $this->form->rememberValues();
            $this->form->addOutput("Password did not match.");
            return false;
        }
        $user = new User();
        $user->setDb($this->di->get("dbqb"));
        $user->username = $username;
        $user->email = $email;
        $user->points = 0;
        $user->setPassword($password);
        $user->save();

        $this->form->addOutput("User was created.");
        return true;
    }
}
