<?php declare(strict_types=1);

namespace App\Http\Controller;

use App\Database\Repository\TodoRepository;


class TodoController extends AbstractController
{
    private $todoRepo;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->todoRepo = new TodoRepository($this->db());
    }

    public function index()
    {
        return $this->view("layout.app")
            ->extendWithView("contents", "home");
    }

    public function list()
    {
        $todos = $this->todoRepo->all();
        return $this->json([
            "status" => "success",
            "todos" => $todos
        ]);
    }

    public function create()
    {
        $content = "";
        $status = "";
        if($this->request->has("todo-content")) {
            $content = $this->request->get("todo-content");
        }
        if($this->request->has("todo-status")) {
            $status = $this->request->get("todo-status");
        }

        $ret = $this->todoRepo->add($content, $status);

        return $this->json([
            "status" => "success",
            "todo" => $ret
        ]);
    }

    public function update($id)
    {   
        $content = "";
        $status = "";
        if($this->request->has("todo-content")) {
            $content = $this->request->get("todo-content");
        }
        if($this->request->has("todo-status")) {
            $status = $this->request->get("todo-status");
        }
        $todo = $this->todoRepo->update($id, $content, $status);
        return $this->json([
            "status" => "success",
            "todo" => $todo
        ]);
    }

    public function remove($id)
    {
        $status = $this->todoRepo->remove($id);
        if($status === true) {
            return $this->json([
                "status" => "success",
            ]);
        }
        return $this->json([
            "status" => "error",
            "error" => [
                "db" => "couldn't delete todo from db"
            ]
        ]);
    }

    public function removeSeveral() {
        $ids = [];
        if($this->request->has("todo-ids")) {
            $ids = $this->request->get("todo-ids");
        }
        // print_r($ids);
        $status = $this->todoRepo->removeMultiple($ids);
        if($status === true) {
            return $this->json([
                "status" => "success",
            ]);
        }
        return $this->json([
            "status" => "error",
            "error" => [
                "db" => "couldn't delete multiple todos from db"
            ]
        ]);
    }
}