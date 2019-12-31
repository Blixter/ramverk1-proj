<?php

namespace Blixter\Answer;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModel;

/**
 * A database driven model using the Active Record design pattern.
 */
class UserVoteOnAnswer extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "UserVoteOnAnswer";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $userId;
    public $answerId;
    public $vote;

    /**
     * Returns the the row of the tag.
     * @param object $di service container.
     *
     * @param array $tId TagId.
     *
     * @return array $row of tag.
     */
    public function checkIfVoted($answerId, $userId)
    {
        $result = $this->findWhere("answerId = ? and userId = ?", [$answerId, $userId]);
        // return $result;
        if ($result->id == null) {
            return false;
        }
        return true;
    }

    /**
     * Returns the vote "up" or "down".
     *
     * @param array $answerId Id of question.
     * @param array $userId Id of user.
     *
     * @return string $result->vote.
     */
    public function getVote($answerId, $userId)
    {
        $result = $this->findWhere("answerId = ? and userId = ?", [$answerId, $userId]);

        return $result->vote;
    }
}
