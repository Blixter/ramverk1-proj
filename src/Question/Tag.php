<?php

namespace Blixter\Question;

// use Anax\DatabaseActiveRecord\ActiveRecordModel;
use Blixter\ActiveRecord\ActiveRecordModelExtra;

/**
 * A database driven model using the Active Record design pattern.
 */
class Tag extends ActiveRecordModelExtra
{
    /**
     * @var string $tableName name of the database table.
     */
    protected $tableName = "Tag";

    /**
     * Columns in the table.
     *
     * @var integer $id primary key auto incremented.
     */
    public $id;
    public $tagName;

    /**
     * Returns the the row of the tag.
     * @param object $di service container.
     *
     * @param array $tId TagId.
     *
     * @return array $row of tag.
     */
    public function getTagInfo($di, $tId): array
    {
        $db = $di->get("db");
        $db->connect();
        $tagArray = [];
        foreach ($tId as $key => $value) {
            $sql = "SELECT * FROM Tag WHERE id = $value->tagId";
            $res = $db->executeFetch($sql);
            array_push($tagArray, $res);
        }
        return $tagArray;
    }
}
