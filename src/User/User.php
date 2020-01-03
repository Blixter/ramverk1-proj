<?php

namespace Blixter\User;

use Anax\DatabaseActiveRecord\ActiveRecordModel;

// use Blixter\ActiveRecord\ActiveRecordModel;

/**
 * A database driven model.
 */
class User extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "User";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $username;
    public $email;
    public $password;
    public $points;

    /**
     * Set the password.
     *
     * @param string $password the password to use.
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify the username and the password, if successful the object contains
     * all details from the database row.
     *
     * @param string $username  username to check.
     * @param string $password the password to use.
     *
     * @return boolean true if username and password matches, else false.
     */
    public function verifyPassword($username, $password)
    {
        $this->find("username", $username);
        return password_verify($password, $this->password);
    }

    /**
     * Returns most active User
     *
     *
     * @return array Most active user.
     */
    public function getMostActiveUser(): array
    {

        $this->checkDb();
        return $this->db->connect()
            ->select("User.id,
                User.userName,
                User.email,
                ((SELECT count(Question.id) FROM Question WHERE Question.userId = User.id) +
                (SELECT count(Answer.id) FROM Answer WHERE Answer.userId = User.id) +
                (SELECT count(Comment.id) FROM Comment WHERE Comment.userId = User.id)) as activepoints")
            ->from($this->tableName)
            ->orderBy("activepoints DESC")
            ->groupBy("User.id")
            ->limit(3)
            ->execute()
            ->fetchAllClass(get_class($this));
    }

    /**
     * Returns email and username for user.
     *
     *
     * @return object User information
     */
    public function getUserInfo($id): object
    {
        return $this->findWhere("id = ?", $id, "User.email, User.username");
    }

    /**
     * Returns email and username for user.
     *
     *
     * @return string User total votes
     */
    public function getVotesByUser($id): string
    {
        return $this->getQuestionVotesByUser($id)->votes
         + $this->getAnswerVotesByUser($id)->votes;
    }

    /**
     * Returns email and username for user.
     *
     *
     * @return string User total points
     */
    public function getPointsByUser($id): string
    {
        return $this->getQuestionPointsByUser($id)->totalPoints
         + $this->getAnswerPointsByUser($id)->totalPoints;
    }

    /**
     * Returns activepoints for the user
     *
     *
     * @return string User total points
     */
    public function getActivePointsByUser($id): object
    {
        $params = [$id];
        $this->checkDb();
        return $this->db->connect()
            ->select("User.id,
                User.userName,
                User.email,
                ((SELECT count(Question.id) FROM Question WHERE Question.userId = User.id) +
                (SELECT count(Answer.id) FROM Answer WHERE Answer.userId = User.id) +
                (SELECT count(Comment.id) FROM Comment WHERE Comment.userId = User.id)) as activepoints")
            ->from($this->tableName)
            ->orderBy("activepoints DESC")
            ->groupBy("User.id")
            ->where("User.id = ?")
            ->execute($params)
            ->fetchInto($this);
    }

    /**
     * Returns email and username for user.
     *
     *
     * @return string User total points
     */
    public function getReputationByUser($id): string
    {
        return $this->getQuestionPointsByUser($id)->totalPoints
         + $this->getAnswerPointsByUser($id)->totalPoints
         + $this->getActivePointsByUser($id)->activepoints;
    }

    /**
     * Returns email and username for user.
     *
     *
     * @return string User total votes
     */
    public function getQuestionVotesByUser($id): object
    {
        $params = [$id];
        $this->checkDb();
        return $this->db->connect()
            ->select("COUNT(id) AS votes")
            ->where("userId = ?")
            ->from("UserVoteOnQuestion")
            ->execute($params)
            ->fetchInto($this);
    }

    /**
     * Returns email and username for user.
     *
     *
     * @return string User total votes
     */
    public function getAnswerVotesByUser($id): object
    {
        $params = [$id];
        $this->checkDb();
        return $this->db->connect()
            ->select("COUNT(id) AS votes")
            ->where("userId = ?")
            ->from("UserVoteOnAnswer")
            ->execute($params)
            ->fetchInto($this);
    }

    /**
     * Returns email and username for user.
     *
     *
     * @return string User total votes
     */
    public function getAnswerPointsByUser($id): object
    {
        $params = [$id];
        $this->checkDb();
        return $this->db->connect()
            ->select("SUM(points) AS totalPoints")
            ->where("userId = ?")
            ->from("Answer")
            ->execute($params)
            ->fetchInto($this);
    }

    /**
     * Returns email and username for user.
     *
     *
     * @return string User total votes
     */
    public function getQuestionPointsByUser($id): object
    {
        $params = [$id];
        $this->checkDb();
        return $this->db->connect()
            ->select("SUM(points) AS totalPoints")
            ->where("userId = ?")
            ->from("Question")
            ->execute($params)
            ->fetchInto($this);
    }

}
