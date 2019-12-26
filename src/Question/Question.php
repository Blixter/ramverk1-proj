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
    public function voteQuestion($questionId, $vote, $userId, $di)
    {


        $userVoteOnQ = new UserVoteOnQuestion();
        $userVoteOnQ->setDb($di->get("dbqb"));

        $voted = $userVoteOnQ->checkIfVoted($questionId, $userId);
        $question = $this->findById($questionId);
        $this->id = $question->id;
        $this->title = $question->title;
        $this->question = $question->question;
        $this->userId = $question->userId;
        $this->created = $question->created;
        $this->points = $question->points;

        if ($voted) {
            var_dump("voted true");
            $result = $userVoteOnQ->findWhere("questionId = ? AND userId = ?", [$questionId, $userId]);
            // print_r($result);
            $previousVote = $result->vote;
            if ($previousVote == "up" AND $vote == "up") {
                var_dump("previous vote up and vote up", $this->points);
                $this->points = $this->points - 1;
                $userVoteOnQ->deleteWhere("questionId = ? AND userId = ?", [$questionId, $userId]);
                return $this->updateWhere("id = ?", $questionId);
            } else if ($previousVote == "down" AND $vote == "down") {
                var_dump("previous vote down and vote down", $this->points);
                $this->points = $this->points + 1;
                $userVoteOnQ->deleteWhere("questionId = ? AND userId = ?", [$questionId, $userId]);
                return $this->updateWhere("id = ?", $questionId);
            } else if ($previousVote == "up") {
                $this->points = $this->points - 1;
            } else if ($previousVote == "down") {
                $this->points = $this->points + 1;
            }
            $this->updateWhere("id = ?", $questionId);
            $userVoteOnQ->deleteWhere("questionId = ? AND userId = ?", [$questionId, $userId]);
            // $userVoteOnQ->id = $result->id;
            // $userVoteOnQ->questionId = $questionId;
            // $userVoteOnQ->userId = $userId;
            // $userVoteOnQ->updateWhere("id = ?", $result->id);

            if ($vote == "up") {
                var_dump("voted and up");
                $this->points = $this->points + 1;
            } else {
                var_dump("voted and down");
                $this->points = $this->points - 1;
            }

            $userVoteOnQ = new UserVoteOnQuestion();
            $userVoteOnQ->setDb($di->get("dbqb"));
            $userVoteOnQ->questionId = $questionId;
            $userVoteOnQ->userId = $userId;
            $userVoteOnQ->vote = $vote;
            $userVoteOnQ->save();
        } else {
            var_dump("voted false");
            if ($vote == "up") {
                var_dump("not voted and up");
                $this->points = $this->points + 1;
            } else {
                var_dump("not voted and down");
                $this->points = $this->points - 1;
            }
            $userVoteOnQ = new UserVoteOnQuestion();
            $userVoteOnQ->setDb($di->get("dbqb"));
            $userVoteOnQ->questionId = $questionId;
            $userVoteOnQ->userId = $userId;
            $userVoteOnQ->vote = $vote;
            $userVoteOnQ->save();
        }
        return $this->updateWhere("id = ?", $questionId);
    }
    

    
    /**
     * Reset vote
     *
     *
     * @return array Most active user.
     */
    public function resetVote($questionId, $vote, $previousVote)
    {

        $question = $this->findById($questionId);
        
        $this->id = $question->id;
        $this->title = $question->title;
        $this->question = $question->question;
        $this->userId = $question->userId;
        $this->created = $question->created;

        if ($previousVote == "up") {
            $this->points = $question->points - 1;
        } else {
            $this->points = $question->points + 1;
        }
        $this->updateWhere("id = ?", $questionId);

        return $this->voteQuestion($questionId, $vote);

    }
    

}
