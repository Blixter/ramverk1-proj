<?php

namespace Blixter\Question;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;
use Blixter\Answer\Answer;
use Blixter\Comment\Comment;
use Blixter\Comment\HTMLForm\CommentForm;
use Blixter\Question\HTMLForm\CreateForm;
use Blixter\Question\HTMLForm\DeleteForm;
use Blixter\Question\HTMLForm\UpdateForm;
use Blixter\Question\Question;
use Blixter\Question\Tag;
use Blixter\Question\TagToQuestion;
use Michelf\MarkdownExtra;

// use Anax\Route\Exception\ForbiddenException;
// use Anax\Route\Exception\NotFoundException;
// use Anax\Route\Exception\InternalErrorException;

/**
 * A sample controller to show how a controller class can be implemented.
 */
class QuestionController implements ContainerInjectableInterface
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
        $this->tag = new Tag();
        $this->tag->setDb($this->di->get("dbqb"));
        $this->tag2question = new TagToQuestion();
        $this->tag2question->setDb($this->di->get("dbqb"));
        $this->answer = new Answer();
        $this->answer->setDb($this->di->get("dbqb"));
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
        $question = new Question();
        $question->setDb($this->di->get("dbqb"));
        $allQuestions = $question->getSortedQuestions(1000);

        $qWithTags = [];

        for ($i = 0; $i < count($allQuestions); $i++) {

            $currentTags = $this->tag2question->findAllWhereJoin(
                "TagToQuestion.questionId = ?", // Where
                $allQuestions[$i]->id, // Value
                "Tag", // Table to join
                "Tag.id = TagToQuestion.tagId", // Join on
                "TagToQuestion.*, Tag.tagName" // Select
            );

            $answerCount = $this->answer->getAnswersCountForQuestion($allQuestions[$i]->id);

            $questionParsed = MarkdownExtra::defaultTransform($allQuestions[$i]->question);

            array_push($qWithTags,
                [
                    "question" => $allQuestions[$i],
                    "tags" => $currentTags,
                    "questionParsed" => $questionParsed,
                    "answerCount" => $answerCount[0]->count,
                ]);
        };

        $page->add("question/index", [
            "questions" => $qWithTags,
        ]);

        return $page->render([
            "title" => "Questions",
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

        $page->add("question/crud/create", [
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

        $page->add("question/crud/delete", [
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

        $page->add("question/crud/update", [
            "form" => $form->getHTML(),
        ]);

        return $page->render([
            "title" => "Update an item",
        ]);
    }

    /**
     * Handler with form to update an item.
     *
     * @param int $id the id to update.
     *
     * @return object as a response object
     */
    public function userAction(int $id): object
    {
        $page = $this->di->get("page");
        $question = new Question();
        $questions = $question->getQuestionsForUser($this->di, $id);

        $qWithTags = [];

        for ($i = 0; $i < count($questions); $i++) {
            $currentTagIds = $this->tag2question->getTagsForQuestion($this->di, $questions[$i]->id);
            $currentTags = $this->tag->getTagInfo($this->di, $currentTagIds);

            array_push($qWithTags,
                [
                    "question" => $questions[$i],
                    "tags" => $currentTags,
                ]);
        };

        $page->add("question/user", [
            "questions" => $qWithTags,
        ]);

        return $page->render([
            "title" => "User questions",
        ]);
    }

    /**
     * Handler with form to update an item.
     *
     * @param int $id the id to update.
     *
     * @return object as a response object
     */
    public function postAction(int $id): object
    {
        $page = $this->di->get("page");
        $question = new Question();
        $question->setDb($this->di->get("dbqb"));

        $question = $question->findWhereJoin(
            "Question.id = ?", // Where
            $id, // Value
            "User", // Table to join
            "Question.userId = User.id", // Join on
            "Question.*, User.username, User.email" // Select
        );

        $currentTags = $this->tag2question->findAllWhereJoin(
            "TagToQuestion.questionId = ?", // Where
            $id, // Value
            "Tag", // Table to join
            "Tag.id = TagToQuestion.tagId", // Join on
            "TagToQuestion.*, Tag.tagName" // Select
        );

        $questionParsed = MarkdownExtra::defaultTransform($question->question);

        $comments = $this->comment->findAllWhereJoin(
            "Comment.postId = ? AND Comment.type = ?", // Where
            [$id, "question"], // Value
            "User", // Table to join
            "User.id = Comment.userId", // Join on
            "Comment.*, User.username, User.email" // Select
        );

        foreach ($comments as $comment) {
            $comment->commentParsed = MarkdownExtra::defaultTransform($comment->comment);
        }

        $answers = $this->answer->getAllAnswers($this->di, $id);
        $answerCount = $this->answer->getAnswersCountForQuestion($id);

        $page->add("question/post", [
            "question" => $question,
            "questionParsed" => $questionParsed,
            "tags" => $currentTags,
            "answers" => $answers,
            "answerCount" => $answerCount[0]->count,
            "comments" => $comments,
        ]);

        return $page->render([
            "title" => "Question",
        ]);
    }

    /**
     * Add a comment to Question
     *
     * @param int $id of the question to add a comment.
     *
     * @return object as a response object
     */
    public function commentAction(int $id): object
    {
        $page = $this->di->get("page");
        $form = new CommentForm($this->di, $id, "question");
        $form->check();
        $question = new Question();
        $question->setDb($this->di->get("dbqb"));
        $question = $question->findWhereJoin(
            "Question.id = ?", // Where
            $id, // Value
            "User", // Table to join
            "Question.userId = User.id", // Join on
            "Question.*, User.username, User.email" // Select
        );

        $questionParsed = MarkdownExtra::defaultTransform($question->question);

        $comments = $this->comment->getAllComments([$id, "question"]);

        foreach ($comments as $comment) {
            $comment->commentParsed = MarkdownExtra::defaultTransform($comment->comment);
        }

        $page->add("question/crud/comment", [
            "question" => $question,
            // "answers" => $answers,
            // "tags" => $tags,
            "form" => $form->getHTML(),
            "questionParsed" => $questionParsed,
            "comments" => $comments,
        ]);
        return $page->render([
            "title" => "Comment question",
        ]);
    }

    /**
     * Add a comment to Question
     *
     *
     * @return object as a response object
     */
    public function voteAction()
    {
        $page = $this->di->get("page");
        $request = $this->di->get("request");
        $vote = $request->getGet("vote");
        $questionId = $request->getGet("questionId");
        $userId = $request->getGet("userId");

        $question = new Question();
        $question->setDb($this->di->get("dbqb"));
        $question->voteQuestion($questionId, $vote, $userId, $this->di);
        $this->di->get("response")->redirect("question/post/" . $questionId)->send();
    }
}
