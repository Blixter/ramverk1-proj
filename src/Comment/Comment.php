<?php

namespace Blixter\Comment;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModel;

/**
 * A database driven model using the Active Record design pattern.
 */
class Comment extends ActiveRecordModel
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "Comment";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     * @var integer "postId" INT NOT NULL,
     * @var string "type" TEXT NOT NULL,
     * @var string "comment" TEXT,
     * @var integer "userId" INTEGER NOT NULL,
     * @var string "created" TIMESTAMP
     */
    public $id;
    public $postId;
    public $type;
    public $comment;
    public $userId;
    public $created;

    /**
     * Returns all comments
     *
     * @param array $value postId and type.
     *
     * @return array $comments for that postId and type.
     */
    public function getAllComments($value)
    {
        $comments = $this->findAllWhereJoin(
            "Comment.postId = ? AND Comment.type = ?", // Where
            $value, // Values
            "User", // Table to join
            "User.id = Comment.userId", // Join on
            "Comment.*, User.username, User.email" // Select
        );

        return $comments;
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
            "comments DESC", // order by
            "Comment.userId", // group by
            "User", // join table
            "Comment.userId = User.id", // join on
            "3", // limit
            "userId, count(userId) as comments, User.username, User.email" // select
        );
    }

}
