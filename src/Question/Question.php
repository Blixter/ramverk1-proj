<?php

namespace Blixter\Question;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModelExtra;

/**
 * A database driven model using the Active Record design pattern.
 */
class Question extends ActiveRecordModelExtra
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
     * Vote on question
     *
     * @param integer $questionId Id of the question
     * @param string $vote up or down vote
     * @param integer $userId Id of the user
     * @param object $di service container
     *
     * @return null
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
            $result = $userVoteOnQ->findWhere("questionId = ? AND userId = ?", [$questionId, $userId]);
            $previousVote = $result->vote;
            if ($this->checkPreviousVote($previousVote, $vote, $questionId, $userId, $di)) {
                return;
            }
            $this->checkVote($vote);
            $this->getAndSaveUserVote($questionId, $userId, $vote, $di);
        } else {
            $this->checkVote($vote);
            $this->getAndSaveUserVote($questionId, $userId, $vote, $di);
        }
        return $this->updateWhere("id = ?", $questionId);
    }

    /**
     * Delete vote
     *
     * @param integer $questionId Id of the question
     * @param integer $userId Id of the user
     * @param object $di service container
     *
     * @return void
     */
    public function deleteVote($questionId, $userId, $di)
    {
        $userVoteOnQ = new UserVoteOnQuestion();
        $userVoteOnQ->setDb($di->get("dbqb"));
        $userVoteOnQ->deleteWhere("questionId = ? AND userId = ?", [$questionId, $userId]);
    }

    /**
     * Get UserVoteOnQuestion object and save vote to table
     *
     * @param integer $questionId Id of the question
     * @param integer $userId Id of the user
     * @param object $di service container
     *
     * @return void
     */
    public function getAndSaveUserVote($questionId, $userId, $vote, $di)
    {
        $userVoteOnQ = new UserVoteOnQuestion();
        $userVoteOnQ->setDb($di->get("dbqb"));
        $userVoteOnQ->questionId = $questionId;
        $userVoteOnQ->userId = $userId;
        $userVoteOnQ->vote = $vote;
        $userVoteOnQ->save();
    }

    /**
     * Check previous vote
     *
     * @param string $previousVote up or down vote
     * @param string $vote up or down vote
     * @param integer $questionId Id of the question
     * @param integer $userId Id of the user
     * @param object $di service container
     *
     * @return void
     */
    public function checkPreviousVote($previousVote, $vote, $questionId, $userId, $di)
    {

        if ($previousVote == "up" and $vote == "up") {
            $this->points = $this->points - 1;
            $this->deleteVote($questionId, $userId, $di);
            $this->updateWhere("id = ?", $questionId);
            return true;
        } else if ($previousVote == "down" and $vote == "down") {
            $this->points = $this->points + 1;
            $this->deleteVote($questionId, $userId, $di);
            $this->updateWhere("id = ?", $questionId);
            return true;
        } else if ($previousVote == "up") {
            $this->points = $this->points - 1;
        } else if ($previousVote == "down") {
            $this->points = $this->points + 1;
        }
        $this->updateWhere("id = ?", $questionId);
        $this->deleteVote($questionId, $userId, $di);
    }

    /**
     * Check Vote and update points.
     *
     * @param string $vote up or down vote
     *
     * @return void
     */
    public function checkVote($vote)
    {
        if ($vote == "up") {
            $this->points = $this->points + 1;
        } else {
            $this->points = $this->points - 1;
        }
    }

}
