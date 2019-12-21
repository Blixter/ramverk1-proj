<?php

namespace Blixter\Startpage;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;
use Blixter\Answer\Answer;
use Blixter\Comment\Comment;
use Blixter\Question\Question;
use Blixter\Question\Tag;
use Blixter\Question\TagToQuestion;
use Blixter\Startpage\Startpage;
use Blixter\User\User;
use Michelf\MarkdownExtra;

// use Anax\Route\Exception\ForbiddenException;
// use Anax\Route\Exception\NotFoundException;
// use Anax\Route\Exception\InternalErrorException;

/**
 * A sample controller to show how a controller class can be implemented.
 */
class StartpageController implements ContainerInjectableInterface
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
        $this->question = new Question();
        $this->question->setDb($this->di->get("dbqb"));
        $this->user = new User();
        $this->user->setDb($this->di->get("dbqb"));
        $this->answer = new Answer();
        $this->answer->setDb($this->di->get("dbqb"));
        $this->tag = new Tag();
        $this->tag->setDb($this->di->get("dbqb"));
        $this->comment = new Comment();
        $this->comment->setDb($this->di->get("dbqb"));
        $this->tag2question = new TagToQuestion();
        $this->tag2question->setDb($this->di->get("dbqb"));
    }

    /**
     * Show all items.
     *
     * @return object as a response object
     */
    public function indexActionGet(): object
    {
        $page = $this->di->get("page");

        $latestQuestions = $this->question->getSortedQuestions(3);
        $mostActiveUser = $this->user->getMostActiveUser();

        $latestQWithTags = [];

        for ($i = 0; $i < count($latestQuestions); $i++) {

            $currentTags = $this->tag2question->findAllWhereJoin(
                "TagToQuestion.questionId = ?", // Where
                $latestQuestions[$i]->id, // Value
                "Tag", // Table to join
                "Tag.id = TagToQuestion.tagId", // Join on
                "TagToQuestion.*, Tag.tagName" // Select
            );

            $answerCount = $this->answer->getAnswersCountForQuestion($latestQuestions[$i]->id);

            $questionParsed = MarkdownExtra::defaultTransform($latestQuestions[$i]->question);

            array_push($latestQWithTags,
                [
                    "question" => $latestQuestions[$i],
                    "tags" => $currentTags,
                    "questionParsed" => $questionParsed,
                    "answerCount" => $answerCount[0]->count,
                ]);
        };

        $popularTags = $this->tag2question->getMostPopularTags(3);

        $page->add("startpage/index", [
            "questions" => $latestQWithTags,
            "popularTags" => $popularTags,
            "mostActiveUser" => $mostActiveUser,
        ]);

        $page->add("startpage/flash", [
            "src" => "img/code.jpg",
        ], "flash");

        return $page->render([
            "title" => "Startpage",
        ]);
    }
}
