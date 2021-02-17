<?php declare(strict_types=1);

use App\Http\Controller\TodoController;


$routes->get("/", [TodoController::class, "index"])->name("home");
$routes->get("/todos", [TodoController::class, "list"])->name("todo.list");
$routes->post("/todos/remove", [TodoController::class, "removeSeveral"])->name("todos.remove");
$routes->post("/todo", [TodoController::class, "create"])->name("todo.create");
$routes->post("/todo/{id}", [TodoController::class, "update"])
    ->where(["id" => "[A-z0-9]+"])
    ->name("todo.update");
$routes->delete("/todo/{id}", [TodoController::class, "remove"])
    ->where(["id" => "[A-z0-9]+"])
    ->name("todo.remove");

// $routes->get(
//     "/",
//     [SimpleController::class, "index"]
// )->name(
//     "home"
// )->middlewares(
//     [\App\Http\Middleware\SimpleMiddleware::class]
// );

// $routes->get(
//     "/{param}",
//     [SimpleController::class, "param"]
// )->where(
//     ["param" => "[0-9]+"]
// )->name(
//     "simple.param"
// );


// $routes->register(
//     ["GET", "DELETE"],
//     "/user/{id}/ ",
//     ["user.controller.class", "show"]
// )->where(
//     ["id" => "[A-z0-9]+"]
// );
// // ->middlewares(
// //     ["user.middleware.class"]
// // )->name(
// //     "user.show"
// // );

// $routes->get(
//     "/post/{id}/ ",
//     ["post.controller.class", "show"]
// )->where(
//     ["id" => "[A-z0-9]+"]
// );
// // ->middlewares(
// //     ["post.middleware.class"]
// // )->name(
// //     "post.show"
// // );
