// constants for denoting todo status
// todos can be either "active" or "completed"
const TODO_STATUS = Object.freeze({
  ACTIVE: 0,
  COMPLETED: 1
});

// constants for denoting which view mode is on
// when "all", app shows all todos i.e. both "active" and "completed"
// and when "active", app shows only active
// and when "completed", app shows only completed
const LIST_VIEW = Object.freeze({
  ACTIVE: TODO_STATUS.ACTIVE,
  COMPLETED: TODO_STATUS.COMPLETED,
  ALL: 2
});

// const DB_STATUS = Object.freeze({
//   ERROR: 1,
//   SUCCESS: 0
// });


// template for showing todo item
// this template is copied and modified by adding todo contents
// and setting status for proper view purposes
// after setting it properly, it is added to todo list
const todoTemplate = document.getElementById("todo-item-template");
if(todoTemplate !== null) {
  console.log("todo item template retreived successfully...");
}

// templates is the section added inside html which deleted
// from html contents when javascript i.e. app.js loads
const templates = document.getElementById("templates");
if(templates !== null) {
  templates.parentNode.removeChild(templates);
}

// function which returns "root" url without trailing slashes
const getOrigin = function () {
  let origin = window.location.origin;
  if(origin[origin.length - 1] === "/") {
    origin = origin.substr(0, origin.length - 1);
  }
  return origin;
};

// returns current page's url without trailing slashes
const getUrl = function () {
  let url = window.location.toString();
  if(url[url.length - 1] === "/") {
    url = url.substr(0, url.length - 1);
  }
  return url;
};

// todo factory creates a Todo object
// it supports data hiding through closures
// and returns a Todo object with methods
const todoFactory = function(o) {
  // check provided object `o` for todo data
  // if not exist, then assign null
  // `null` has been chosen for keeping consistency:
  //  "either it exists or its null"
  let id = (o.id === null || o.id === undefined) ? null : o.id;
  let content = (o.content === null || o.content === undefined) ? null : o.content;
  let status = (o.status === null || o.status === undefined) ? null: o.status;

  return {
    // notice, id is returned through a method id()
    // because of data hiding
    // same goes for other fields
    id() {
      return id;
    },
    status() {
      return status;
    },
    content() {
      return content;
    },
    // update takes an object as input which contains
    // either status or content or both fields
    // if any of the field(or both) is mismatched
    // it returns a new Todo object
    // otherwise just returns null
    update(u) {
      let _status = status;
      let _content = content;
      if(u.status !== null && u.status !== undefined) {
        _status = u.status;
      }
      if(u.content !== null && u.content !== undefined) {
        _content = u.content;
      }
      if(_content === content && _status === status) {
        return null;
      }
      return todoFactory({
        id,
        status: _status,
        content: _content
      });
    }
  };
};


// cache factory returns a Cache object
// it provides data hiding through closures
// it returns a object with necessary methods
// for cache storing, removing and updating
const cacheFactory = function() {
  // todos field contains all cached Todo object
  let todos = {};
  // count field keeps track of "active" and "completed"
  // Todo object count
  let count = [];
  // initializing count with 0 for both "active" and "completed"
  count[TODO_STATUS.ACTIVE] = 0;
  count[TODO_STATUS.COMPLETED] = 0;

  return {
    // notice, `todos` provided by methods
    // its only for the facility of data hiding
    // so that, data remains consistent
    // and doesn't change by accident
    todos() {
      return todos;
    },
    // returns the Todo object with provided id
    // if not exists just return null
    get(id) {
      if(todos[id] !== null) {
        return todos[id];
      }
      return null;
    },
    // add Todo to cache
    // also update `count` status
    // add method only adds todo if it does not exist
    add(todo) {
      // only add Todo if it does not exist
      if(todos[todo.id()] === null || todos[todo.id()] === undefined) {
        todos[todo.id()] = todo;
        count[todo.status()]++;
        return todo;
      }
      // if exists already, then just return null
      return null;
    },
    // removes data from cache
    // and retuns the Todo object on successful delete
    // on failure returns null
    remove(todo) {
      if(todos[todo.id()] !== null) {
        delete todos[todo.id()];
        count[todo.status()]--;
        return todo;
      }
      return null;
    },
    // updates Todo in cache
    // update only if Todo object exists already
    // else just return null
    // also, notice it updates the count
    update(todo) {
      let old = todos[todo.id()];
      if(old !== null && old !== undefined) {
        console.log("old: " + old.id() + " " + old.status());
        console.log("todo: " + todo.id() + " " + todo.status());
        count[old.status()]--;
        count[todo.status()]++;
        todos[todo.id()] = todo;
        return todo;
      }
      return null;
    },
    // returns total no of Todo object in cache
    total() {
      return count[TODO_STATUS.ACTIVE] + count[TODO_STATUS.COMPLETED];
    },
    // returns no of active Todo object in cache
    active() {
      return count[TODO_STATUS.ACTIVE];
    },
    // returns no of completed Todo object in cache
    completed() {
      return count[TODO_STATUS.COMPLETED];
    }
  };
};

// xhrRequest is a function which provides a very easy way
// to send XMLHttpRequest(i.e. ajax) request to server
// rather than doing a normal xhr request directly, it
// returns a Promise object
// so, one must use it through .then() or await
const xhrRequest = function(request) {
  // returning Promise
  return new Promise((resolve, reject) => {
    // create a new XMLHttpRequest object
    let xhr = new XMLHttpRequest();
    xhr.open(request.method, request.url);
    xhr.addEventListener("readystatechange", function() {
      // when response returned successfully
      // resolve it
      if(this.readyState === 4 && this.status === 200) {
        let response = JSON.parse(this.responseText);
        resolve(response);
      }
    });
    // if error occurred, then reject the promise
    // with `status: error` message
    xhr.addEventListener("error", function() {
      reject({
        "status": "error",
        "error": {
          "httpStatus": this.status,
          "statusText": this.statusText
        }
      });
    });
    // note, this xhr request always sends `application/x-www-form-urlencoded`
    // request. for our usecase it enough for now
    // any changes need to made will be done later
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    // send data if the method is post
    // we'll add PUT/PATCH support, if needed
    if(request.method === "POST") {
      xhr.send(request.data);
    } else {
      // else just send for methods anyhting other than post
      xhr.send();
    }
  });
};

// process server side returned data to a Todo object
// note: By `Todo object`, I mean a object returned by
//       todoFactory() function
const processTodoData = function(todoData) {
  let status = TODO_STATUS.ACTIVE;
  if(todoData.status === "completed") {
    status = TODO_STATUS.COMPLETED;
  }
  let todo = todoFactory({
    id: todoData.id,
    content: todoData.content,
    status: status
  });
  return todo;
};

// this function returns an object which
// returns csrftoken value using a method
// called value()
const csrfTokenFactory = function() {
  const csrfToken = document.getElementById("csrfmiddlewaretoken");
  return {
    value() {
      return csrfToken.value;
    }
  }
};

// converts `Todo object` data to server side
// representable data. This conversion is necessary
// to send data to server
const todoUrlencodedData = function(todo) {
  let data = "";
  if(todo.id() !== null && todo.id() !== undefined) {
    data = "todo-id=" + todo.id();
  }
  if(todo.content() !== null && todo.content() !== undefined) {
    if(data !== "") {
      data += "&";
    }
    data += "todo-content=" + todo.content();
  }
  if(todo.status() !== null && todo.status() !== undefined) {
    if(data !== "") {
      data += "&";
    }
    let status = "active";
    if(todo.status() === TODO_STATUS.COMPLETED) {
      status = "completed";
    }
    data += "todo-status=" + status;
  }
  return data;
}


// databaseFactory returns a `Database object`
// `Database object` abstracts away all the necessary
// operations for database which provides a very
// convenient to way to talk to database.
// on note: `Cache object` is always in sync with `Database object`
const databaseFactory = function() { 
  return {
    // returns all todos from database
    async all() {
      let url = getOrigin() + "/todos";
      // send a request to database and wait until response
      // arive.
      // note: it does not block the execution cause
      //       promise is used to resolve response
      let response = await xhrRequest({
        method: "GET",
        url: url
      });
      // by default `todos` is null
      // if `todos` remains null till the end
      // it means some error occured during database operations
      // note: error is printed in browser console
      let todos = null;
      if(response.status === "success") {
        todos = {};
        for(let id of Object.keys(response.todos)) {
          // convert server side data representation to `Todo object`
          todos[id] = processTodoData(response.todos[id]);
        }
      } else {
        console.log("Error while pulling all from database...");
        console.log(response.status);
        if(response.status === "error") {
          console.log(response.error);
        }
      }
      return todos;
    },
    // adds data to database
    // takes `Todo object` as input
    async add(todo) {
      let url = getOrigin() + "/todo";
      // csrfmiddlwaretoken must be added when sending post request
      let data = "csrfmiddlewaretoken=" + csrfTokenFactory().value();
      data += "&" + todoUrlencodedData(todo);
      let response = await xhrRequest({
        method: "POST",
        url: url,
        data: data
      });
      // by default todoRet is null
      // if at the end null is returned, it means
      // some error occured
      // and error is logged in browser console
      let todoRet = null;
      if(response.status === "success") {
        todoRet = processTodoData(response.todo);
      } else {
        console.log("Error while adding new todo to database...");
        console.log(response.status);
        if(response.status === "error") {
          console.log(response.error);
        }
      }
      return todoRet;
    },
    // removes single todo from database
    // takes a `Todo object` as input
    // upon success returns `true`
    // and on fail returns `false`
    async remove(todo) {
      let url = getOrigin() + "/todo/" + todo.id();
      let response = await xhrRequest({
        method: "DELETE",
        url: url
      });
      if(response.status === "success") {
        console.log(todo.id() + " deleted successfully");
        return true;
      }
      console.log("couldn't delete " + todo.id());
      return false;
    },
    // updates todo data in database
    // takes a `Todo object` as input which contains
    // updated data. Note: `id` should be same as old `Todo object``
    // upon success returns updated `Todo object``
    async update(todo) {
      let url = getOrigin() + "/todo/" + todo.id();
      let data = "csrfmiddlewaretoken=" + csrfTokenFactory().value();
      data += "&" + todoUrlencodedData(todo);
      let response = await xhrRequest({
        method: "POST",
        url: url,
        data: data
      });
      // by default todoRet is null
      // if null returns, that means
      // nothing to update
      // or couldn't update data
      // if fails, then it prints error to browser console
      let todoRet = null;
      if(response.status === "success") {
        todoRet = processTodoData(response.todo);
      } else {
        console.log("coudn't update todo " + todo.id());
      }
      return todoRet;
    },
    // removes multiple todos from database
    // in this case, we just send todos id as an array to delete
    // upon success, it returns true
    // on fail, just returns false
    async removeSeveral(bin) {
      let url = getOrigin() + "/todos/remove";
      let data = "csrfmiddlewaretoken=" + csrfTokenFactory().value();
      for(let todo of bin) {
        if(data !== "") {
          data += "&";
        }
        data += "todo-ids[]=" + todo.id();
      }
      let response = await xhrRequest({
        url: url,
        method: "POST",
        data: data
      });
      if(response.status === "success") {
        console.log("cleared completed todos from db...");
        return true;
      }
      console.log("couldn't clear completed todos from db...");
      return false;
    }
  };
};

const listViewFactory = function() {
  const list = document.querySelector(".todo-item-list");
  return {
    clear() {
      list.textContent = "";
    },
    get(id) {
      return document.getElementById(id);
    },
    add(node) {
      if(node !== null) {
        list.appendChild(node);
      }
    },
    remove(node) {
      if(node !== null) {
        list.removeChild(node);
      }
    },
    update(newNode, oldNode) {
      if(newNode !== null && oldNode !== null) {
        list.replaceChild(newNode, oldNode);
      }
    }
  }
};

const todoItemViewFactory = function(todo) {
  const node = todoTemplate.cloneNode(true);
  const content = node.querySelector(".todo-item-content");
  const checkboxLabel = node.querySelector(".todo-checkbox");
  const checkboxInput = node.querySelector(".todo-checkbox input");
  const removeBtn = node.querySelector(".todo-item-remove");
  node.id = todo.id();
  content.textContent = todo.content();
  content.addEventListener("click", itemContentClickListener);
  checkboxLabel.addEventListener("change", checkboxListener);
  removeBtn.addEventListener("click", removeBtnListener);
  node.update = function(todo) {
    content.textContent = todo.content();
    if(todo.status() === TODO_STATUS.COMPLETED) {
      checkboxInput.checked = true;
      content.style.textDecoration = "line-through";
    } else {
      checkboxInput.checked = false;
      content.style.textDecoration = "";
    }
  };
  node.update(todo);
  return node;
};

const expansionSymbolViewFactory = function() {
  let expansionSymbol = document.querySelector(".expansion-symbol");
  return {
    hide() {
      expansionSymbol.style.visibility = "hidden";
    },
    show() {
      expansionSymbol.style.visibility = "visible";
    }
  };
};

const itemSectionViewFactory = function() {
  let itemSection = document.querySelector(".todo-item-section");
  return {
    hide() {
      itemSection.hidden = true;
    },
    show() {
      itemSection.hidden = false;
    }
  };
};

const activeItemViewFactory = function() {
  let activeItem = document.querySelector(".todo-active-item-count");
  return {
    update(count) {
      let x = (count <= 1) ? " item " : " items ";
      activeItem.textContent = count + x + "left";
    }
  };
};

const functionBtnViewFactory = function() {
  let functionBtns = document.getElementsByClassName("todo-function-btn");
  return {
    markPressed(pressedBtn) {
      for(let btn of functionBtns) {
        btn.style.borderColor = "white";
      }
      pressedBtn.style.borderColor = "#c7c5c5";
    }
  };
};

const clearCompletedViewFactory = function() {
  let clearCompletedBtn = document.querySelector(".clear-completed-btn");
  return {
    hide() {
      clearCompletedBtn.style.visibility = "hidden";
    },
    show() {
      clearCompletedBtn.style.visibility = "visible";
    }
  };
}

const viewFactory = function() {
  let mode = LIST_VIEW.ALL; // by default
  const expansionSymbol = expansionSymbolViewFactory();
  const activeItem = activeItemViewFactory();
  const clearCompleted = clearCompletedViewFactory();
  const functionBtn = functionBtnViewFactory();
  const itemSection = itemSectionViewFactory();
  const list = listViewFactory();

  const modeBtn = function(_mode) {
    let btnClass = "";
    if(_mode == LIST_VIEW.ALL) {
      btnClass = ".all-btn";
    } else if(_mode === LIST_VIEW.ACTIVE) {
      btnClass = ".active-btn";
    } else {
      btnClass = ".completed-btn";
    }
    return document.querySelector(btnClass);
  }

  return {
    add(data) {
      let node = todoItemViewFactory(data.todo);
      if(mode === LIST_VIEW.ALL || mode == data.todo.status()) {
        list.add(node);
      }
      activeItem.update(data.active);
      if(data.completed === 0) {
        clearCompleted.hide();
      } else {
        clearCompleted.show();
      }
      itemSection.show();
      expansionSymbol.show();
    },
    remove(data) {
      let node = list.get(data.todo.id());
      list.remove(node);
      activeItem.update(data.active);
      if(data.completed === 0) {
        clearCompleted.hide();
        if(data.active === 0) {
          itemSection.hide();
          expansionSymbol.hide();
        }
      }
    },
    update(data) {
      let node = list.get(data.todo.id());
      node.update(data.todo);
      if(mode !== LIST_VIEW.ALL) {
        if(mode !== data.todo.status()) {
          list.remove(node);
        }
      }
      activeItem.update(data.active);
      if(data.completed > 0){
        clearCompleted.show();
      } else {
        clearCompleted.hide();
      }
    },
    setMode(newMode, data) {
      if(mode !== newMode) {
        list.clear();
        let todos = data.todos;
        for(let id of Object.keys(todos)) {
          todo = todos[id];
          let node = todoItemViewFactory(todo);
          if(newMode === LIST_VIEW.ALL || newMode === todo.status()) {
            list.add(node);
          }
        }
        activeItem.update(data.active);
        if(data.completed === 0) {
          clearCompleted.hide();
        } else {
          clearCompleted.show();
        }
        if(data.completed + data.active > 0) {
          itemSection.show();
          expansionSymbol.show();
        } else {
          itemSection.hide();
          expansionSymbol.hide();
        }
        functionBtn.markPressed(modeBtn(newMode));
        mode = newMode;
      }
    }
  };
};

const inputFieldFactory = function() {
  let inputField = document.querySelector(".todo-form-content");
  return {
    content() {
      return inputField.value.trim();
    },
    clear() {
      inputField.value = "";
    }
  };
};

const db = databaseFactory();
const cache = cacheFactory();
const view = viewFactory();
const input = inputFieldFactory();

async function inputListener(ev) {
  if(ev.key === "Enter" || ev.keyCode === 13) {
    ev.preventDefault();
    let content = input.content();
    input.clear();
    if(content !== "") {
      let todo = todoFactory({
        content: content,
        status: TODO_STATUS.ACTIVE
      });
      let todoRet = await db.add(todo);
      if(todoRet !== null) {
        view.add({
          todo: cache.add(todoRet),
          active: cache.active(),
          completed: cache.completed()
        });
      }
    }
  }
}

async function checkboxListener(ev) {
  ev.preventDefault();
  let node = ev.target.closest(".todo-item");
  let oldTodo = cache.get(node.id);
  let todo = null;
  if(ev.target.checked) {
    todo = oldTodo.update({
      status: TODO_STATUS.COMPLETED
    });
  } else {
    todo = oldTodo.update({
      status: TODO_STATUS.ACTIVE
    });
  }
  if(todo !== null) {
    let todoRet = await db.update(todo);
    if(todoRet !== null) {
      view.update({
        todo: cache.update(todoRet),
        completed: cache.completed(),
        active: cache.active()
      });
    }
  }
}

function itemContentClickListener(ev) {
  ev.preventDefault();
  console.log("item clicked");
  let div = ev.target;
  let divParent = div.parentNode;
  let input = document.createElement("input");
  input.value = div.textContent;
  input.style.padding = "12px";
  input.style.fontSize = "20px";
  input.style.border = "2px solid white";
  divParent.textContent = "";
  divParent.appendChild(input);
  input.focus();
  const itemInputListener = async function(ev) {
    ev.preventDefault();
    let content = input.value.trim();
    let oldContent = div.textContent;
    div.textContent = content;
    divParent.textContent = "";
    divParent.appendChild(div);
    let updateFailed = true;
    if(content !== "") {
      let node = div.closest(".todo-item");
      let oldTodo = cache.get(node.id);
      let todo = oldTodo.update({
        content: content
      });
      if(todo !== null) {
        let todoRet = await db.update(todo);
        if(todoRet !== null) {
          view.update({
            todo: cache.update(todoRet),
            completed: cache.completed(),
            active: cache.active()
          });
          updateFailed = false;
        }
      }
    }
    if(updateFailed) {
      div.textContent = oldContent;
    }
  };
  input.addEventListener("focusout", itemInputListener);
  input.addEventListener("keyup", (ev) => {
    if(ev.key === "Enter" || ev.keyCode === 13) {
      itemInputListener(ev);
    }
  });
}

async function removeBtnListener(ev) {
  ev.preventDefault();
  let node = ev.target.closest(".todo-item");
  let todo = cache.get(node.id);
  let removeStatus = await db.remove(todo);
  if(removeStatus === true) {
    view.remove({
      todo: cache.remove(todo),
      completed: cache.completed(),
      active: cache.active()
    });
  }
}

function allBtnListener(ev) {
  ev.preventDefault();
  view.setMode(LIST_VIEW.ALL, {
    todos: cache.todos(),
    completed: cache.completed(),
    active: cache.active()
  });
}

function activeBtnListener(ev) {
  ev.preventDefault();
  view.setMode(LIST_VIEW.ACTIVE, {
    todos: cache.todos(),
    completed: cache.completed(),
    active: cache.active()
  });
}

function completedBtnListener(ev) {
  ev.preventDefault();
  view.setMode(LIST_VIEW.COMPLETED, {
    todos: cache.todos(),
    completed: cache.completed(),
    active: cache.active()
  });
}

async function clearCompletedBtnListener(ev) {
  ev.preventDefault();
  if(cache.completed() > 0) {
    let todos = cache.todos();
    let bin = [];
    for(let id of Object.keys(todos)) {
      let todo = todos[id];
      if(todo.status() === TODO_STATUS.COMPLETED) {
        console.log(todo);
        bin.push(todo);
      }
    }
    let response = await db.removeSeveral(bin);
    if(response === true) {
      for(let todo of bin) {
        view.remove({
          todo: cache.remove(todo),
          completed: cache.completed(),
          active: cache.active()
        });
      }
    }
  }
}

function addListener(cls, event, listener) {
  let node = document.querySelector(cls);
  if(node !== null) {
    node.addEventListener(event, listener);
  } else {
    console.log("error while adding listener: " + cls + " not found");
  }
}

async function pullAll() {
  let todos = await db.all();
  for(let id of Object.keys(todos)) {
    view.add({
      todo: cache.add(todos[id]),
      completed: cache.completed(),
      active: cache.active()
    });
  }
}

function initApp() {
  pullAll();
  addListener(".todo-form-content", "keyup", inputListener);
  addListener(".all-btn", "click", allBtnListener);
  addListener(".active-btn", "click", activeBtnListener);
  addListener(".completed-btn", "click", completedBtnListener);
  addListener(".clear-completed-btn", "click", clearCompletedBtnListener);
}

initApp();
