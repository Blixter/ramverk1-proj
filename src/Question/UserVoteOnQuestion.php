<?php

namespace Blixter\Question;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModel;

/**
 * A database driven model using the Active Record design pattern.
 */
class UserVoteOnQuestion extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "UserVoteOnQuestion";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $userId;
    public $questionId;
    public $vote;

    /**
     * Returns the the row of the tag.
     * @param object $di service container.
     *
     * @param array $tId TagId.
     *
     * @return array $row of tag.
     */
    public function checkIfVoted($questionId, $userId)
    {
        $result = $this->findWhere("questionId = ? and userId = ?", [$questionId, $userId]);
        // return $result;
        if ($result->id == null) {
            return false;
        }
        return true;
    }

    /**
     * Returns the vote "up" or "down".
     *
     * @param array $questionId Id of question.
     * @param array $userId Id of user.
     *
     * @return string $result->vote.
     */
    public function getVote($questionId, $userId)
    {
        $result = $this->findWhere("questionId = ? and userId = ?", [$questionId, $userId]);

        return $result->vote;
    }
}
