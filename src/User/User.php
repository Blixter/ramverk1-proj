<?php

namespace Blixter\User;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModel;

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
        return $this->findAllJoinOrderByGroupBy(
            "activepoints DESC", // order by
            "User.id", // group by
            "Question", // join table
            "User.id = Question.userId", // join on
            "3", // limit
            "User.id,
            User.userName,
            User.email,
            ((SELECT count(Question.id) FROM Question WHERE Question.userId = User.id) +
            (SELECT count(Answer.id) FROM Answer WHERE Answer.userId = User.id) +
            (SELECT count(Comment.id) FROM Comment WHERE Comment.userId = User.id)) as activepoints" // select
        );
    }

//     SELECT
    // User.id,
    // User.userName,
    // User.email,
    // ((SELECT count(Question.id) FROM Question WHERE Question.userId = User.id) +
    // (SELECT count(Answer.id) FROM Answer WHERE Answer.userId = User.id) +
    // (SELECT count(Comment.id) FROM Comment WHERE Comment.userId = User.id)) as activepoints
    // FROM User
    // JOIN Question ON User.id = Question.userId
    // GROUP BY User.id
    // ORDER BY "activepoints" DESC
    // LIMIT 3;

}
