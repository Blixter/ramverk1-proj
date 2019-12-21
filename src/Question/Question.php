<?php

namespace Blixter\Question;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModel;

/**
 * A database driven model using the Active Record design pattern.
 */
class Question extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "Question";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     * @var string $title TEXT.
     * @var string $question TEXT.
     * @var integer $userId INTEGER NOT NULL.
     * @var integer $created INTEGER NOTT NULL.
     * @var integer $points INTEGER NOT NULL.
     */
    public $id;
    public $title;
    public $question;
    public $userId;
    public $created;
    public $points;

    // /**
    //  * Returns the latest questions sorted by date created.
    //  *
    //  * @param $di A service container.
    //  * @param integer $amount Number of questions.
    //  *
    //  * @return array $questions With latest questions.
    //  */
    // public function getSortedQuestions($di, $amount): array
    // {
    //     $db = $di->get("db");
    //     $db->connect();

    //     $amountQuestions = $amount ? "LIMIT $amount" : null;

    //     $sql = "SELECT * FROM Question ORDER BY created DESC $amountQuestions";
    //     $questions = $db->executeFetchAll($sql);
    //     return $questions;
    // }

    /**
     * Returns the latest questions sorted by date created.
     *
     * @param $di A service container.
     * @param integer $amount Number of questions.
     *
     * @return array $questions With latest questions.
     */
    public function getSortedQuestions($amount): array
    {
        $questions = $this->findAllJoinOrderBy(
            "created DESC", // Order By
            "User", // Table to join
            "Question.userId = User.id", // Join on
            $amount, // Limit
            "Question.*, User.username, User.email" // Select

        );

        return $questions;
        // $db = $di->get("db");
        // $db->connect();

        // $amountQuestions = $amount ? "LIMIT $amount" : null;

        // $sql = "SELECT * FROM Question ORDER BY created DESC $amountQuestions";
        // $questions = $db->executeFetchAll($sql);
        // return $questions;
    }

    /**
     * Returns all questsions by user sorted by date.
     *
     * @param integer $uId UserId of User.
     *
     * @return array $questions by the User.
     */
    public function getQuestionsForUser($uId): array
    {

        $questions = $this->findAllWhereJoinOrderBy(
            "userId = ?", // Where
            $uId, // Value
            "User", // Table to join
            "Question.userId = User.id", // Join on
            "created", // Order By
            "Question.*, User.username, User.email" // Select
        );

        // $db = $di->get("db");
        // $db->connect();

        // $sql = "SELECT * FROM Question WHERE userId = $uId ORDER BY created";
        // $questions = $db->executeFetchAll($sql);
        return $questions;
    }

    /**
     * Returns most active User
     *
     *
     * @return array Most active user.
     */
    public function getMostActiveUser(): array
    {
        // select userId,
        // count(userId)  as question,
        // User.username from Question
        // Join User on Question.userId = User.id
        // Group By userId
        // Order By count DESC;

        return $this->findAllJoinOrderByGroupBy(
            "questions DESC", // order by
            "Question.userId", // group by
            "User", // join table
            "Question.userId = User.id", // join on
            "3", // limit
            "userId, count(userId) as questions, User.username, User.email" // select
        );
    }

    /**
     * Returns most active User
     *
     *
     * @return array Most active user.
     */
    public function voteQuestion($questionId, $userId, $vote): array
    {
        // select userId,
        // count(userId)  as question,
        // User.username from Question
        // Join User on Question.userId = User.id
        // Group By userId
        // Order By count DESC;

        return $this->findAllJoinOrderByGroupBy(
            "questions DESC", // order by
            "Question.userId", // group by
            "User", // join table
            "Question.userId = User.id", // join on
            "3", // limit
            "userId, count(userId) as questions, User.username, User.email" // select
        );
    }

}
