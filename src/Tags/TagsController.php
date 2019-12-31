<?php

namespace Blixter\Tags;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;
use Blixter\Answer\Answer;
use Blixter\Question\Tag;
use Blixter\Question\TagToQuestion;
use Michelf\MarkdownExtra;

// use Anax\Route\Exception\ForbiddenException;
// use Anax\Route\Exception\NotFoundException;
// use Anax\Route\Exception\InternalErrorException;

/**
 * A sample controller to show how a controller class can be implemented.
 */
class TagsController implements ContainerInjectableInterface
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
        $this->tags = new Tag();
        $this->tags->setDb($this->di->get("dbqb"));
        $this->answer = new Answer();
        $this->answer->setDb($this->di->get("dbqb"));
        $this->tag2quest = new TagToQuestion();
        $this->tag2quest->setDb($this->di->get("dbqb"));
    }

    /**
     * Description.
     *
     * @param datatype $variable Description
     *
     * @throws Exception
     *
     * @return object as a response object
     */
    public function indexActionGet(): object
    {
        $page = $this->di->get("page");

        $allTags = $this->tags->findAll();

        $page->add("tags/all-tags", [
            "tags" => $allTags,
        ]);

        return $page->render([
            "title" => "All tags",
        ]);
    }

    /**
     * Description.
     * @param int $id the id to fetch.
     *
     * @throws Exception
     *
     * @return object as a response object
     */
    public function getAction(int $id): object
    {
        $page = $this->di->get("page");

        $questions = $this->tag2quest->getQuestionsForTag($id);

        $qWithTags = [];

        for ($i = 0; $i < count($questions); $i++) {

            $currentTags = $this->tag2quest->findAllWhereJoin(
                "TagToQuestion.questionId = ?", // Where
                $questions[$i]->id, // Value
                "Tag", // Table to join
                "Tag.id = TagToQuestion.tagId", // Join on
                "TagToQuestion.*, Tag.tagName" // Select
            );

            $answerCount = $this->answer->getAnswersCountForQuestion($questions[$i]->id);
            $questionParsed = MarkdownExtra::defaultTransform($questions[$i]->question);
            array_push($qWithTags,
                [
                    "question" => $questions[$i],
                    "tags" => $currentTags,
                    "questionParsed" => $questionParsed,
                    "answerCount" => $answerCount[0]->count,
                ]);
        };

        $page->add("tags/index", [
            "questions" => $qWithTags,
        ]);

        return $page->render([
            "title" => "Questions with tag",
        ]);
    }
}
