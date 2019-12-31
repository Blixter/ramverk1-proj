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

    /**
     * Returns all answers by user sorted by date.
     *
     * @param integer $uId UserId of User.
     *
     * @return array $answers by the User.
     */
    public function getAnswersForUser($uId): array
    {

        $answers = $this->findAllWhereJoinOrderBy(
            "userId = ?", // Where
            $uId, // Value
            "User", // Table to join
            "Answer.userId = User.id", // Join on
            "created", // Order By
            "Answer.*, User.username, User.email" // Select
        );

        return $answers;
    }

    /**
     * Vote on an answer
     *
     *
     * @return void
     */
    public function voteAnswer($answerId, $vote, $userId, $di)
    {

        $userVoteOnA = new UserVoteOnAnswer();
        $userVoteOnA->setDb($di->get("dbqb"));

        $voted = $userVoteOnA->checkIfVoted($answerId, $userId);
        $answer = $this->findById($answerId);
        $this->id = $answer->id;
        $this->answer = $answer->answer;
        $this->userId = $answer->userId;
        $this->created = $answer->created;
        $this->points = $answer->points;
        $this->accepted = $answer->accepted;

        if ($voted) {
            $result = $userVoteOnA->findWhere("answerId = ? AND userId = ?", [$answerId, $userId]);
            $previousVote = $result->vote;
            if ($previousVote == "up" and $vote == "up") {
                $this->points = $this->points - 1;
                $userVoteOnA->deleteWhere("answerId = ? AND userId = ?", [$answerId, $userId]);
                return $this->updateWhere("id = ?", $answerId);
            } else if ($previousVote == "down" and $vote == "down") {
                $this->points = $this->points + 1;
                $userVoteOnA->deleteWhere("answerId = ? AND userId = ?", [$answerId, $userId]);
                return $this->updateWhere("id = ?", $answerId);
            } else if ($previousVote == "up") {
                $this->points = $this->points - 1;
            } else if ($previousVote == "down") {
                $this->points = $this->points + 1;
            }
            $this->updateWhere("id = ?", $answerId);
            $userVoteOnA->deleteWhere("answerId = ? AND userId = ?", [$answerId, $userId]);

            if ($vote == "up") {
                $this->points = $this->points + 1;
            } else {
                $this->points = $this->points - 1;
            }

            $userVoteOnA = new UserVoteOnAnswer();
            $userVoteOnA->setDb($di->get("dbqb"));
            $userVoteOnA->answerId = $answerId;
            $userVoteOnA->userId = $userId;
            $userVoteOnA->vote = $vote;
            $userVoteOnA->save();
        } else {
            if ($vote == "up") {
                $this->points = $this->points + 1;
            } else {
                $this->points = $this->points - 1;
            }
            $userVoteOnA = new UserVoteOnAnswer();
            $userVoteOnA->setDb($di->get("dbqb"));
            $userVoteOnA->answerId = $answerId;
            $userVoteOnA->userId = $userId;
            $userVoteOnA->vote = $vote;
            $userVoteOnA->save();
        }
        return $this->updateWhere("id = ?", $answerId);
    }

    /**
     * Updates the answer to accepted
     *
     * @param integer $answerId Id of the answer.
     *
     * @return void
     */
    public function acceptAnswer($answerId, $user)
    {
        $answer = $this->findById($answerId);
        if ($user == $answer->userId) {
            $this->id = $answer->id;
            $this->answer = $answer->answer;
            $this->userId = $answer->userId;
            $this->created = $answer->created;
            $this->points = $answer->points;
            $this->accepted = true;
            return $this->updateWhere("id = ?", $answerId);
        } else {
            return null;
        }
    }
}
