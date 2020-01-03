<?php

namespace Blixter\Comment\HTMLForm;

use Anax\HTMLForm\FormModel;
use Blixter\Answer\Answer;
use Blixter\Comment\Comment;
use Blixter\Question\Question;
use Psr\Container\ContainerInterface;

/**
 * Form to create an item.
 */
class CommentForm extends FormModel
{
    /**
     * Constructor injects with DI container.
     *
     * @param Psr\Container\ContainerInterface $di a service container
     */
    public function __construct(ContainerInterface $di, $id, $type)
    {
        parent::__construct($di);
        $post = $type == "question" ? $this->getQuestionDetails($id) : $this->getAnswerDetails($id);
        $this->form->create(
            [
                "id" => __CLASS__,
                "escape-values" => false,
            ],
            [
                "post" => [
                    "type" => "hidden",
                    "value" => $post->id,
                    "validation" => ["not_empty"],
                ],
                "type" => [
                    "type" => "hidden",
                    "value" => $type,
                    "validation" => ["not_empty"],
                ],
                "comment" => [
                    "class" => "form-control",
                    "type" => "textarea",
                    "validation" => ["not_empty"],
                ],
                "submit" => [
                    "class" => "btn btn-primary",
                    "type" => "submit",
                    "value" => "Send comment",
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
    public function getQuestionDetails($id): object
    {
        $question = new Question();
        $question->setDb($this->di->get("dbqb"));
        $question->find("id", $id);
        return $question;
    }
    /**
     * Get details on item to load form with.
     *
     * @param integer $id get details on item with id.
     *
     * @return Answer
     */
    public function getAnswerDetails($id): object
    {
        $answer = new Answer();
        $answer->setDb($this->di->get("dbqb"));
        $answer->find("id", $id);
        return $answer;
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
        $comment = new Comment();
        $comment->setDb($this->di->get("dbqb"));
        $comment->userId = $session->get("login");
        $comment->comment = $this->form->value("comment");
        $comment->postId = $this->form->value("post");
        $comment->type = $this->form->value("type");
        $comment->created = time();
        $comment->save();
        return true;
    }

    /**
     * Callback what to do if the form was successfully submitted, this
     * happen when the submit callback method returns true. This method
     * can/should be implemented by the subclass for a different behaviour.
     */
    public function callbackSuccess()
    {
        if ($this->form->value("type") == "question") {
            $this->di->get("response")->redirect("question/post/" . $this->form->value("post"))->send();
        } else {
            $answerDetails = $this->getAnswerDetails($this->form->value("post"));
            $this->di->get("response")->redirect("question/post/" . $answerDetails->questionId)->send();
        }
    }

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
