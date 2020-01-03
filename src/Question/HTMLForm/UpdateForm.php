<?php

namespace Blixter\Question\HTMLForm;

use Anax\HTMLForm\FormModel;
use Blixter\Question\Question;
use Psr\Container\ContainerInterface;

/**
 * Form to update an item.
 */
class UpdateForm extends FormModel
{
    /**
     * Constructor injects with DI container and the id to update.
     *
     * @param Psr\Container\ContainerInterface $di a service container
     * @param integer             $id to update
     */
    public function __construct(ContainerInterface $di, $id)
    {
        parent::__construct($di);
        $question = $this->getItemDetails($id);
        $this->form->create(
            [
                "id" => __CLASS__,
                "legend" => "Update details of the item",
                "escape-values" => false,
            ],
            [
                "id" => [
                    "type" => "text",
                    "validation" => ["not_empty"],
                    "readonly" => true,
                    "value" => $question->id,
                ],

                "title" => [
                    "type" => "text",
                    "validation" => ["not_empty"],
                    "value" => $question->title,
                ],

                "question" => [
                    "type" => "text",
                    "validation" => ["not_empty"],
                    "value" => $question->question,
                ],

                "userId" => [
                    "type" => "text",
                    "validation" => ["not_empty"],
                    "readonly" => true,
                    "value" => $question->userId,
                ],

                "submit" => [
                    "type" => "submit",
                    "value" => "Save",
                    "callback" => [$this, "callbackSubmit"],
                ],

                "reset" => [
                    "type" => "reset",
                ],
            ]
        );
    }

    /**
     * Get details on item to load form with.
     *
     * @param integer $id get details on item with id.
     *
     * @return Question
     */
    public function getItemDetails($id): object
    {
        $question = new Question();
        $question->setDb($this->di->get("dbqb"));
        $question->find("id", $id);
        return $question;
    }

    /**
     * Callback for submit-button which should return true if it could
     * carry out its work and false if something failed.
     *
     * @return bool true if okey, false if something went wrong.
     */
    public function callbackSubmit(): bool
    {
        $question = new Question();
        $question->setDb($this->di->get("dbqb"));
        $question->find("id", $this->form->value("id"));
        $question->column1 = $this->form->value("column1");
        $question->column2 = $this->form->value("column2");
        $question->save();
        return true;
    }

    // /**
    //  * Callback what to do if the form was successfully submitted, this
    //  * happen when the submit callback method returns true. This method
    //  * can/should be implemented by the subclass for a different behaviour.
    //  */
    // public function callbackSuccess()
    // {
    //     $this->di->get("response")->redirect("question")->send();
    //     //$this->di->get("response")->redirect("question/update/{$question->id}");
    // }

    // /**
    //  * Callback what to do if the form was unsuccessfully submitted, this
    //  * happen when the submit callback method returns false or if validation
    //  * fails. This method can/should be implemented by the subclass for a
    //  * different behaviour.
    //  */
    // public function callbackFail()
    // {
    //     $this->di->get("response")->redirectSelf()->send();
    // }
}
