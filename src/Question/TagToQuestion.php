<?php

namespace Blixter\Question;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModel;

/**
 * A database driven model using the Active Record design pattern.
 */
class TagToQuestion extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "TagToQuestion";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $tagId;
    public $questionId;

    /**
     * Return rows of most used tags.
     *
     * @param integer $amount Limit.
     *
     * @return array $tags most used Tags.
     */
    public function getMostPopularTags($amount): array
    {
        return $this->findAllJoinOrderByGroupBy(
            "count DESC", // order by
            "TagToQuestion.tagId", // group by
            "Tag", // join table
            "TagToQuestion.tagId = Tag.id", // join on
            $amount, // limit
            "TagToQuestion.tagId, count(tagId) as count, Tag.tagName" // select
        );
    }

    /**
     * Return rows of all tags for current question
     *
     * @param object $di service container.
     *
     * @param integer $qId QuestionId.
     *
     * @return array $tags for currentQuestion.
     */
    public function getTagsForQuestion($di, $qId): array
    {
        $db = $di->get("db");
        $db->connect();
        $sql = "SELECT tagId FROM TagToQuestion WHERE questionId = $qId";

        $res = $db->executeFetchAll($sql);
        return $res;
    }

    /**
     * Return rows of all tags for current question
     *
     * @param object $di service container.
     *
     * @param integer $tId TagId.
     *
     * @return array $tags for currentQuestion.
     */
    public function getQuestionsForTag($di, $tId): array
    {
        $db = $di->get("db");
        $db->connect();
        $sql = "SELECT * FROM TagToQuestion AS t2q
        LEFT JOIN Question AS q ON t2q.questionId = q.id
        WHERE tagId = $tId";

        $res = $db->executeFetchAll($sql);
        return $res;
    }
}
