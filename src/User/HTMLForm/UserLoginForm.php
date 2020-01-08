<?php

namespace Blixter\User\HTMLForm;

use Anax\HTMLForm\FormModel;
use Blixter\User\User;
use Psr\Container\ContainerInterface;

/**
 * Example of FormModel implementation.
 */
class UserLoginForm extends FormModel
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
                "legend" => "User Login",
            ],
            [
                "username" => [
                    "class" => "form-control",
                    "type" => "text",
                    "validation" => ["not_empty"],
                ],

                "password" => [
                    "class" => "form-control",
                    "type" => "password",
                    "validation" => ["not_empty"],
                ],

                "submit" => [
                    "type" => "submit",
                    "class" => "btn btn-primary",
                    "value" => "Login",
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
        $password = $this->form->value("password");

        $user = new User();
        $user->setDb($this->di->get("dbqb"));
        $res = $user->verifyPassword($username, $password);

        if (!$res) {
            $this->form->rememberValues();
            $this->form->addOutput("User or password did not match.");
            return false;
        }

        // $this->form->addOutput("User " . $user->username . " logged in.");

        $session = $this->di->get("session");
        $session->set("login", $user->id);

        return true;
    }

    /**
     * Callback what to do if the form was successfully submitted, this
     * happen when the submit callback method returns true. This method
     * can/should be implemented by the subclass for a different behaviour.
     */
    public function callbackSuccess()
    {
        $this->di->get("response")->redirect("")->send();
    }

    /**
     * Callback what to do if the form was unsuccessfully submitted, this
     * happen when the submit callback method returns false or if validation
     * fails. This method can/should be implemented by the subclass for a
     * different behaviour.
     */
    public function callbackFail()
    {
        $this->di->get("response")->redirectSelf()->send();
    }
}
