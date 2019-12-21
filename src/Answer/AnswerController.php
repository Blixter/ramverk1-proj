<?php

namespace Blixter\Answer;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;
use Blixter\Answer\HTMLForm\CreateForm;
use Blixter\Answer\HTMLForm\DeleteForm;
use Blixter\Answer\HTMLForm\UpdateForm;
use Blixter\Comment\Comment;
use Blixter\Comment\HTMLForm\CommentForm;
use Blixter\Question\Question;
use Michelf\MarkdownExtra;

// use Anax\Route\Exception\ForbiddenException;
// use Anax\Route\Exception\NotFoundException;
// use Anax\Route\Exception\InternalErrorException;

/**
 * A sample controller to show how a controller class can be implemented.
 */
class AnswerController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;

    /**
     * @var $data description
     */
    //private $data;

    /**
     * The initialize method is optional and will always be called before the
     * target method/action. This is a convienient method where you could
     * setup internal properties that are commonly used by several methods.
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->comment = new Comment();
        $this->comment->setDb($this->di->get("dbqb"));
    }

    /**
     * Show all items.
     *
     * @return object as a response object
     */
    public function indexActionGet(): object
    {
        $page = $this->di->get("page");
        $answer = new Answer();
        $answer->setDb($this->di->get("dbqb"));

        $page->add("answer/crud/view-all", [
            "items" => $answer->findAll(),
        ]);

        return $page->render([
            "title" => "A collection of items",
        ]);
    }

    /**
     * Handler with form to create a new item.
     *
     * @return object as a response object
     */
    public function createAction(): object
    {
        $page = $this->di->get("page");
        $form = new CreateForm($this->di);
        $form->check();

        $page->add("answer/crud/create", [
            "form" => $form->getHTML(),
        ]);

        return $page->render([
            "title" => "Create a item",
        ]);
    }

    /**
     * Handler with form to delete an item.
     *
     * @return object as a response object
     */
    public function deleteAction(): object
    {
        $page = $this->di->get("page");
        $form = new DeleteForm($this->di);
        $form->check();

        $page->add("answer/crud/delete", [
            "form" => $form->getHTML(),
        ]);

        return $page->render([
            "title" => "Delete an item",
        ]);
    }

    /**
     * Handler with form to update an item.
     *
     * @param int $id the id to update.
     *
     * @return object as a response object
     */
    public function updateAction(int $id): object
    {
        $page = $this->di->get("page");
        $form = new UpdateForm($this->di, $id);
        $form->check();

        $page->add("answer/crud/update", [
            "form" => $form->getHTML(),
        ]);

        return $page->render([
            "title" => "Update an item",
        ]);
    }

    /**
     * Show item.
     *
     * @return object as a response object
     */
    public function questionAction(int $questionId): object
    {
        $page = $this->di->get("page");
        $form = new CreateForm($this->di, $questionId);
        $form->check();
        $question = new Question();
        $question->setDb($this->di->get("dbqb"));
        // $where, $value, $joinTable, $joinOn, $select
        $question->findWhereJoin(
            "Question.id = ?", // Where
            $questionId, // Value
            "User", // Table to join
            "Question.userId = User.id", // Join on
            "Question.*, User.username, User.email" // Select
        );

        $answer = new Answer();
        $answer->setDb($this->di->get("dbqb"));

        $questionParsed = MarkdownExtra::defaultTransform($question->question);

        $page->add("question/crud/answer", [
            "question" => $question,
            "form" => $form->getHTML(),
            // "comments" => $questionComments,
            // "answers" => $answers ?? null,
            "questionParsed" => $questionParsed,
        ]);
        return $page->render([
            "title" => "Answer question",
        ]);
    }

    /**
     * Add a comment to an answer
     *
     * @param int $id of the answer to add a comment.
     *
     * @return object as a response object
     */
    public function commentAction(int $id): object
    {
        $page = $this->di->get("page");
        $form = new CommentForm($this->di, $id, "answer");
        $form->check();
        // $tag = new Tag();
        // $tag->setDb($this->di->get("dbqb"));
        // $tags = $tag->findAllWhere("question = ?", $questionId);
        $answer = new Answer();
        $answer->setDb($this->di->get("dbqb"));
        // $question = $question->find("id", $id);
        $answer = $answer->findWhereJoin(
            "Answer.id = ?", // Where
            $id, // Value
            "User", // Table to join
            "Answer.userId = User.id", // Join on
            "Answer.*, User.username, User.email" // Select
        );
        // $question->getQuestionObject("Question.id", $questionId);

        $answerParsed = MarkdownExtra::defaultTransform($answer->answer);

        $comments = $this->comment->findAllWhereJoin(
            "Comment.postId = ? AND Comment.type = ?", // Where
            [$id, "answer"], // Value
            "User", // Table to join
            "User.id = Comment.userId", // Join on
            "Comment.*, User.username, User.email" // Select
        );

        foreach ($comments as $comment) {
            $comment->commentParsed = MarkdownExtra::defaultTransform($comment->comment);
        }

        // $answer = new Answer();
        // $answer->setDb($this->di->get("dbqb"));
        // $answers = $answer->findAllAnswers($question->id);
        // foreach ((array) $answers as $key => $value) {
        //     $value->html = MarkdownExtra::defaultTransform($value->answer);
        //     $answerComment = new Comment();
        //     $answerComment->setDb($this->di->get("dbqb"));
        //     $answers[$key]->comments = $answerComment->findAllComments([$value->id, "answer"]);
        //     foreach ($answers[$key]->comments as $comment) {
        //         $comment->html = MarkdownExtra::defaultTransform($comment->text);
        //     }
        // }
        $page->add("question/crud/comment-answer", [
            "answer" => $answer,
            // "answers" => $answers,
            // "tags" => $tags,
            "form" => $form->getHTML(),
            "answerParsed" => $answerParsed,
            "comments" => $comments,
        ]);
        return $page->render([
            "title" => "Comment question",
        ]);
    }

}
