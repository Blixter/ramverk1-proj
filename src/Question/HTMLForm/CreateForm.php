<?php

namespace Blixter\Question\HTMLForm;

use Anax\HTMLForm\FormModel;
use Blixter\Question\Question;
use Blixter\Question\Tag;
use Blixter\Question\TagToQuestion;
use Psr\Container\ContainerInterface;

/**
 * Form to create an item.
 */
class CreateForm extends FormModel
{
    /**
     * Constructor injects with DI container.
     *
     * @param Psr\Container\ContainerInterface $di a service container
     */
    public function __construct(ContainerInterface $di)
    {
        parent::__construct($di);
        // Get all Tags from database.
        $tag = new Tag();
        $tag->setDb($this->di->get("dbqb"));
        $tags = $tag->findAll();
        // Store the tag names in an array
        $tagArray = [];
        foreach ($tags as $key => $value) {
            array_push($tagArray, $value->tagName);
        }
        $this->form->create(
            [
                "id" => __CLASS__,

            ],
            [
                "title" => [
                    "type" => "text",
                    "class" => "form-control",
                    "validation" => ["not_empty"],
                ],

                "question" => [
                    "type" => "textarea",
                    "class" => "form-control",
                    "validation" => ["not_empty"],
                ],

                "tags" => [
                    "type" => "checkbox-multiple",
                    "label" => "Select one ore more tags",
                    "values" => $tagArray,
                    "checked" => [],
                ],

                "submit" => [
                    "type" => "submit",
                    "class" => "btn btn-primary",
                    "value" => "Create item",
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
        $session = $this->di->get("session");
        if ($session->has("login")) {
            $question = new Question();
            $question->setDb($this->di->get("dbqb"));
            $question->title = $this->form->value("title");
            $question->question = $this->form->value("question");
            $question->created = time();
            $question->userId = $session->get("login");
            $question->points = 0;

            $questionId = $question->save();

            $tag = new Tag();
            $tag->setDb($this->di->get("dbqb"));

            foreach ($this->form->value("tags") as $currentTag) {
                // Get TagToQuestion Table
                $tag2question = new TagToQuestion();
                $tag2question->setDb($this->di->get("dbqb"));
                // Get current tag row from database.
                $tagInfo = $tag->find("tagName", $currentTag);
                // Save tagId and questionId to Table.
                $tag2question->tagId = $tagInfo->id;
                $tag2question->questionId = $questionId;
                $tag2question->save();
            }

            return true;
        }
        return false;
    }

    /**
     * Callback what to do if the form was successfully submitted, this
     * happen when the submit callback method returns true. This method
     * can/should be implemented by the subclass for a different behaviour.
     */
    public function callbackSuccess()
    {
        $this->di->get("response")->redirect("question")->send();
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
