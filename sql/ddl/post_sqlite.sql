--
-- Table Question
--
DROP TABLE IF EXISTS Question;
CREATE TABLE Question (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "title" TEXT,
    "question" TEXT, 
    "userId" INTEGER NOT NULL,
    "created" DATETIME,
    "points" INTEGER NOT NULL DEFAULT 0

);

--
-- Table Answer
--
DROP TABLE IF EXISTS Answer;
CREATE TABLE Answer (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "questionId" INTEGER NOT NULL, 
    "userId" INTEGER NOT NULL, 
    "answer" TEXT, 
    "created" DATETIME,
    "accepted" BOOLEAN NOT NULL DEFAULT false,
    "points" INTEGER NOT NULL DEFAULT 0
);

--
-- Table Tag
--
DROP TABLE IF EXISTS Tag;
CREATE TABLE Tag (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "tagName" TEXT
);

--
-- Table TagToQuestion
--
DROP TABLE IF EXISTS TagToQuestion;
CREATE TABLE TagToQuestion (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "tagId" INTEGER NOT NULL,
    "QuestionId" INTEGER NOT NULL
);

--
-- Table UserVoteOnQuestion
--
DROP TABLE IF EXISTS UserVoteOnQuestion;
CREATE TABLE UserVoteOnQuestion (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "userId" INTEGER NOT NULL,
    "questionId" INTEGER NOT NULL,
    "vote" TEXT 
);

--
-- Table UserVoteOnAnswer
--
DROP TABLE IF EXISTS UserVoteOnAnswer;
CREATE TABLE UserVoteOnAnswer (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "userId" INTEGER NOT NULL,
    "answerId" INTEGER NOT NULL,
    "vote" TEXT 
);

--
-- Table Comment
--
DROP TABLE IF EXISTS Comment;
CREATE TABLE Comment (
    "id" INTEGER PRIMARY KEY NOT NULL,
    "postId" INT NOT NULL,
    "type" TEXT NOT NULL,
    "comment" TEXT,
    "userId" INTEGER NOT NULL,
    "created" TIMESTAMP
);