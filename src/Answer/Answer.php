<?php

namespace Blixter\Answer;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModel;
use Blixter\Comment\Comment;
use Michelf\MarkdownExtra;

/**
 * A database driven model using the Active Record design pattern.
 */
class Answer extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "Answer";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     * @var integer $questionId INTEGER NOT NULL.
     * @var integer $userId INTEGER NOT NULL.
     * @var string $answer TEXT.
     * @var string $created DATETIME.
     * @var integer $points INTEGER NOT NULL DEFAULT 0.
     */
    public $id;
    public $questionId;
    public $userId;
    public $answer;
    public $created;
    public $points;

    /**
     * Returns all answers with comments.
     *
     * @param $di A service container.
     * @param integer $questionId id of the question.
     *
     * @return array $questions by the User.
     */
    public function getAllAnswers($di, $questionId): array
    {
        // $where, $value, $joinTable, $joinOn, $select
        $answers = $this->findAllWhereJoin(
            "Answer.questionId = ?", // Where
            $questionId, // Value
            "User", // Table to join
            "User.id = Answer.userId", // Join on
            "Answer.*, User.username, User.email" // Select
        );
        foreach ((array) $answers as $key => $value) {
            $value->answerParsed = MarkdownExtra::defaultTransform($value->answer);
            $comment = new Comment();
            $comment->setDb($di->get("dbqb"));
            // $answers[$key]->isUser = $answers[$key]->user === $userId;
            $answers[$key]->comments = $comment->getAllComments([$value->id, "answer"]);
            foreach ($answers[$key]->comments as $comment) {
                $comment->commentParsed = MarkdownExtra::defaultTransform($comment->comment);
            }
        }
        return $answers;
    }

    /**
     * Returns amounts of Answer for question.
     *
     * @param integer $id id of the question.
     *
     * @return array Most active user.
     */
    public function getAnswersCountForQuestion($id): array
    {
        return $this->findAllWhere(
            "questionId = ?", // Where
            $id, // value
            "count(id) as count" // select
        );
    }

    /**
     * Returns most active User
     *
     *
     * @return array Most active user.
     */
    public function getMostActiveUser(): array
    {
        return $this->findAllJoinOrderByGroupBy(
            "answers DESC", // order by
            "Answer.userId", // group by
            "User", // join table
            "Answer.userId = User.id", // join on
            "3", // limit
            "userId, count(userId) as answers, User.username, User.email" // select
        );
    }
}
