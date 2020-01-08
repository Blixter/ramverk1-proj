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
        try {
            $user = new User();
            $user->setDb($this->di->get("dbqb"));
            $user->username = $username;
            $user->email = $email;
            $user->points = 0;
            $user->setPassword($password);
            $user->save();
            return true;
        } catch (\Anax\Database\Exception\Exception $e) {
            return false;
        }
        return true;
    }
    /**
     * Callback what to do if the form was successfully submitted, this
     * happen when the submit callback method returns true. This method
     * can/should be implemented by the subclass for a different behaviour.
     */
    public function callbackSuccess()
    {
        $this->form->addOutput("User was created.");
        $this->di->get("response")->redirectSelf()->send();
    }
    // /**
    //  * Callback what to do if the form was unsuccessfully submitted, this
    //  * happen when the submit callback method returns false or if validation
    //  * fails. This method can/should be implemented by the subclass for a
    //  * different behaviour.
    //  */
    public function callbackFail()
    {
        //$this->form->rememberValues();
        $this->form->addOutput("Username or Email is already taken, try something else.");
        $this->di->get("response")->redirectSelf()->send();
    }
}
