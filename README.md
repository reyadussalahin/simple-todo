<!-- ![GitHub Workflow Status](https://img.shields.io/github/workflow/status/reyadussalahin/simple-todo/Automated%20Test%20and%20Deploy%20Simple%20Todo%20App%20to%20Heroku?style=flat) -->
<!-- [![Issues](https://img.shields.io/github/issues/reyadussalahin/simple-todo?style=flat&color=blue)](https://github.com/reyadussalahin/simple-todo/issues) -->
<!-- [![License](https://img.shields.io/github/license/reyadussalahin/simple-todo?color=teal&style=flat)](https://github.com/reyadussalahin/simple-todo/blob/master/LICENSE) -->
<!-- [![Forks](https://img.shields.io/github/forks/reyadussalahin/simple-todo?style=flat&color=purple)](https://github.com/reyadussalahin/simple-todo/network/members) -->
<!-- [![Stars](https://img.shields.io/github/stars/reyadussalahin/simple-todo?style=flat)](https://github.com/reyadussalahin/simple-todo/stargazers) -->

<h1 align="center">Simple Todo</h1>

<p align="center">
    <span>
        <a href="https://github.com/reyadussalahin/simple-todo/actions">
            <img alt="GitHub Workflow Status" src="https://img.shields.io/github/workflow/status/reyadussalahin/simple-todo/Automated%20Test%20and%20Deploy%20Simple%20Todo%20App%20to%20Heroku?style=flat">
        </a>
    </span>
    <span>
        <a href="https://github.com/reyadussalahin/simple-todo/issues">
            <img alt="GitHub Workflow Status" src="https://img.shields.io/github/issues/reyadussalahin/simple-todo?style=flat&color=blue">
        </a>
    </span>
    <span>
        <a href="https://github.com/reyadussalahin/simple-todo/blob/main/LICENSE">
            <img alt="GitHub Workflow Status" src="https://img.shields.io/github/license/reyadussalahin/simple-todo?color=teal&style=flat">
        </a>
    </span>
</p>
<br>
Simple todo is a very minimalistic webapp. It has been built to demonstrate the process of building an webapp using `Object Oriented PHP`. Though, this app is very minimalistic in nature, it contains all the basic properties of a good web application. It has been built totally object oriented way using pure PHP(no framework has been used other than PHPUnit, which is only a testing framework), HTML, CSS and JavaScript. At present this app support several good features. I'm going to describe them below.

### Live
I've hosted the app on heroku and I'm using continous integration to ship changes automatically and instantly(more on this later). You may visit the app using the following link:
  
[http://simple-todo-rs.herokuapp.com/](http://simple-todo-rs.herokuapp.com/)


## Features supported by Simple Todo

### Backend features
On the backend I've built the following things:
1. A custom Routing Engine
2. Pretty URL support
3. Middleware support(both generic and route specific)
4. Controller's to handle request
5. Named Routes
6. CSRF protection through middleware
7. A small but extensible and efficient templating engine
8. A convenient oop way to access global variables(note: the way I've implemented it, it is extensble, but not complete yet).

The app is built using totally object oriented way and have strong type support that may detect breaks during development phase and thus increase speed.

### Frontend features
1. A very responsive, fast and consistent UI.
2. Caching mechanism to cache data on client side.
3. Optimized network(i.e. db) calls.

### Testing Support And Comments
For unit testing I'm using PHPUnit. Though, I've already started writing tests and continuing, but still it would take sometime to cover the whole codebase.

About comments, as the app covers a lot of features, I've tried to write the code verbosely, so that one may not need comments to understand the code(to manage my dev time). But still commenting is the de facto for doc. I'll try to take the whole codebase under comments with time.

### Automated Testing and Continous Deployment
1. Automated testing support using Github Actions and PHPUnit.
2. Continous Deployment support using Github Actions and Heroku Git based deployment system.

[Note: the app runs automated testing on each push, but only deploys to heroku if the test is successful].

### Database
For database I've used postgresql. And to handle database operations I've used PHP PDO to perfom insert, delete, update etc. operations.

As a side note, for managing dependency `Composer` has been used.

## For local usage or development
If you want to run this app locally for your own usage or development, you can easily do so. Make sure you've installed PHP, composer and postgresql installed in your pc and an internet connection.
  
At first run composer to install the dependencies:
```bash
$ php composer.phar install
```
  
Now, create a `.env` in the project root directory and put your db info there as follows:
```bash
DATABASE_URL=postgres://<username>:<password>@<hostname>:<port>/<dbname>
```

You may also want to put other info in your .env file(do as per your need)

And then run:
```bash
$ php database/migrate.php
```

After creating db table you may run the tests:
```bash
$ ./vendor/bin/phpunit
```

If tests are successful then, you can run the app using php's development web server as follows:
```bash
$ php -c <path-to-php.ini> -t public/ -S 127.0.0.1:8000
```
  
Now visit `127.0.0.1:8000` on your browser to use the webapp.

## Acknowledgments
I've used [PHPUnit](https://github.com/sebastianbergmann/phpunit) for unit testing and a slightly edited(according to my need) script from [devcoder-xyz/php-dotenv](https://github.com/devcoder-xyz/php-dotenv) to parse `.env` file.

## LICENSE
This repository is published under `MIT License`. To know more about license please visit [this link](https://github.com/reyadussalahin/simple-todo/blob/main/LICENSE).

## Contributing
I am thinking of explaining the whole architecture of this project to everyone who are interested in webapp development. I am thinking of planning a book(or tutorial kind of resource) about how to build an web application from scratch. If you have anything to contribute or advise, I'm all ears. Just give me a knock.
