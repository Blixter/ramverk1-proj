<?php

namespace Blixter\Answer\HTMLForm;

use Anax\HTMLForm\FormModel;
use Blixter\Answer\Answer;
use Blixter\Question\Question;
use Psr\Container\ContainerInterface;

/**
 * Form to create an item.
 */
class CreateForm extends FormModel
{
    /**
     * Constructor injects with DI container.
     *
     * @param \Psr\Container\ContainerInterface $di a service container
     */
    public function __construct(ContainerInterface $di, $questionId)
    {
        parent::__construct($di);
        $question = $this->getItemDetails($questionId);
        $this->form->create(
            [
                "id" => __CLASS__,
            ],
            [
                "question" => [
                    "type" => "hidden",
                    "value" => $question->id,
                    "validation" => ["not_empty"],
                ],
                "answer" => [
                    "type" => "textarea",
                    "class" => "form-control",
                    "validation" => ["not_empty"],
                ],
                "submit" => [
                    "type" => "submit",
                    "class" => "btn btn-primary",
                    "value" => "Send answer",
                    "callback" => [$this, "callbackSubmit"],
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
        $answer = new Answer();
        $answer->setDb($this->di->get("dbqb"));
        $answer->answer = $this->form->value("answer");
        $answer->userId = $this->di->session->get("login");
        $answer->questionId = $this->form->value("question");
        $answer->created = time();
        $answer->points = 0;
        $answer->save();
        return true;
    }

    /**
     * Callback what to do if the form was successfully submitted, this
     * happen when the submit callback method returns true. This method
     * can/should be implemented by the subclass for a different behaviour.
     */
    public function callbackSuccess()
    {
        $question = $this->getItemDetails($this->form->value("question"));
        $this->di->get("response")->redirect("question/post/$question->id")->send();
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
