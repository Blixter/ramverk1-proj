<?php
namespace Blixter\User\HTMLForm;

use Anax\HTMLForm\FormModel;
use Blixter\User\User;
use Psr\Container\ContainerInterface;

/**
 * Form to create an item.
 */
class UserEditForm extends FormModel
{
    /**
     * Constructor injects with DI container.
     *
     * @param \Psr\Container\ContainerInterface $di a service container
     */
    public function __construct(ContainerInterface $di, $loggedinUser)
    {
        parent::__construct($di);
        $this->form->create(
            [
                "id" => __CLASS__,
            ],
            [
                "username" => [
                    "type" => "text",
                    "class" => "form-control",
                    "validation" => ["not_empty"],
                    "value" => $loggedinUser->username,
                ],
                "email" => [
                    "type" => "email",
                    "class" => "form-control",
                    "validation" => ["not_empty"],
                    "value" => $loggedinUser->email,
                ],
                "id" => [
                    "type" => "hidden",
                    "readonly" => "true",
                    "validation" => ["not_empty"],
                    "value" => $loggedinUser->id,
                ],
                "submit" => [
                    "type" => "submit",
                    "value" => "Save",
                    "class" => "btn btn-primary",
                    "callback" => [$this, "callbackSubmit"],
                ],
                "reset" => [
                    "type" => "reset",
                    "value" => "Reset",
                    "class" => "btn btn-secondary",
                ],
            ]
        );
    }
    /**
     * Callback for submit-button which should return true if it could
     * carry out its work and false if something failed.
     *
     * @return bool true if okey, false if something went wrong.
     */
    public function callbackSubmit(): bool
    {
        $user = new User();
        $user->setDb($this->di->get("dbqb"));
        $user->find("id", $this->form->value("id"));
        $user->username = $this->form->value("username");
        $user->email = $this->form->value("email");
        try {
            $user->save();
            return true;
        } catch (\Anax\Database\Exception\Exception $e) {
            return false;
        }
    }
    /**
     * Callback what to do if the form was successfully submitted, this
     * happen when the submit callback method returns true. This method
     * can/should be implemented by the subclass for a different behaviour.
     */
    public function callbackSuccess()
    {
        $this->di->get("response")->redirect("user/profile")->send();
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
