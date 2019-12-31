<?php

namespace Blixter\User;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Commons\ContainerInjectableTrait;
use Blixter\Answer\Answer;
use Blixter\Comment\Comment;
use Blixter\Question\Question;
use Blixter\User\HTMLForm\UserCreateForm;
use Blixter\User\HTMLForm\UserEditForm;
use Blixter\User\HTMLForm\UserLoginForm;
use Blixter\User\User;

// use Anax\Route\Exception\ForbiddenException;
// use Anax\Route\Exception\NotFoundException;
// use Anax\Route\Exception\InternalErrorException;

/**
 * A sample controller to show how a controller class can be implemented.
 */
class UserController implements ContainerInjectableInterface
{
    use ContainerInjectableTrait;

    /**
     * @var $data description
     */
    //private $data;

    // /**
    //  * The initialize method is optional and will always be called before the
    //  * target method/action. This is a convienient method where you could
    //  * setup internal properties that are commonly used by several methods.
    //  *
    //  * @return void
    //  */
    // public function initialize() : void
    // {
    //     ;
    // }

    /**
     * Description.
     *
     * @param datatype $variable Description
     *
     * @throws Exception
     *
     * @return object as a response object
     */
    public function indexActionGet(): object
    {
        $page = $this->di->get("page");

        $page->add("anax/v2/article/default", [
            "content" => "An index page",
        ]);

        return $page->render([
            "title" => "A index page",
        ]);
    }

    /**
     * Description.
     *
     * @param datatype $variable Description
     *
     * @throws Exception
     *
     * @return object as a response object
     */
    public function loginAction(): object
    {
        $page = $this->di->get("page");
        $form = new UserLoginForm($this->di);
        $form->check();

        $page->add("user/login", [
            "form" => $form->getHTML(),
        ]);

        return $page->render([
            "title" => "User login",
        ]);
    }

    /**
     * Description.
     *
     * @param datatype $variable Description
     *
     * @throws Exception
     *
     * @return object as a response object
     */
    public function logoutAction(): object
    {
        $page = $this->di->get("page");
        $session = $this->di->get("session");
        $session->delete("login");

        $this->di->get("response")->redirect("")->send();
    }

    /**
     * Description.
     *
     * @param datatype $variable Description
     *
     * @throws Exception
     *
     * @return object as a response object
     */
    public function createAction(): object
    {
        $page = $this->di->get("page");
        $form = new UserCreateForm($this->di);
        $form->check();

        $page->add("user/register", [
            "form" => $form->getHTML(),
        ]);

        return $page->render([
            "title" => "Register user",
        ]);
    }

    /**
     * Description.
     *
     * @param datatype $variable Description
     *
     * @throws Exception
     *
     * @return object as a response object
     */
    public function viewAction($id): object
    {
        $page = $this->di->get("page");
        $question = new Question();
        $question->setDb($this->di->get("dbqb"));
        $answer = new Answer();
        $answer->setDb($this->di->get("dbqb"));
        $comment = new Comment();
        $comment->setDb($this->di->get("dbqb"));
        $user = new User();
        $user->setDb($this->di->get("dbqb"));

        $questions = $question->getQuestionsForUser($id);
        $answers = $answer->getAnswersForUser($id);
        $comments = $comment->getCommentsForUser($id);
        $user = $user->getUserInfo($id);
        $user->votes = $user->getVotesByUser($id);
        $user->reputation = $user->getPointsByUser($id);

        $page->add("user/user", [
            "questions" => $questions,
            "answers" => $answers,
            "comments" => $comments,
            "user" => $user,
        ]);

        return $page->render([
            "title" => "User page",
        ]);
    }

    /**
     * Show all items.
     *
     * @return object as a response object
     */
    public function profileAction(): object
    {
        $page = $this->di->get("page");

        $user = new User();
        $user->setDb($this->di->get("dbqb"));

        $loggedinUser = $this->di->get("session")->get("login") ?? null;
        $user = $user->findById($loggedinUser);
        if ($loggedinUser == null) {
            return $this->di->get("response")->redirect("user/login");
        }

        $form = new UserEditForm($this->di, $user);
        $form->check();

        $page->add("user/profile", [
            "form" => $form->getHTML(),
        ]);

        return $page->render([
            "title" => "Profile",
        ]);
    }
}
