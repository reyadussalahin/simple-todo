const TODO_STATUS = Object.freeze({
  ACTIVE: 0,
  COMPLETED: 1
});

const LIST_VIEW = Object.freeze({
  ACTIVE: TODO_STATUS.ACTIVE,
  COMPLETED: TODO_STATUS.COMPLETED,
  ALL: 2
});

// const DB_STATUS = Object.freeze({
//   ERROR: 1,
//   SUCCESS: 0
// });

const todoTemplate = document.getElementById("todo-item-template");
if(todoTemplate !== null) {
  console.log("todo item template retreived successfully...");
}

const templates = document.getElementById("templates");
if(templates !== null) {
  templates.parentNode.removeChild(templates);
}

const getOrigin = function () {
  let origin = window.location.origin;
  if(origin[origin.length - 1] === "/") {
    origin = origin.substr(0, origin.length - 1);
  }
  return origin;
};

const getUrl = function () {
  let url = window.location.toString();
  if(url[url.length - 1] === "/") {
    url = url.substr(0, url.length - 1);
  }
  return url;
};

const todoFactory = function(o) {
  let id = (o.id === null || o.id === undefined) ? null : o.id;
  let content = (o.content === null || o.content === undefined) ? null : o.content;
  let status = (o.status === null || o.status === undefined) ? null: o.status;
  return {
    id() {
      return id;
    },
    status() {
      return status;
    },
    content() {
      return content;
    },
    updateStatus(newStatus) {
      if(newStatus !== this.status()) {
        return todoFactory({
          id,
          content,
          status: newStatus
        });
      }
    }
  };
};

const cacheFactory = function() {
  let todos = {};
  let count = [];
  count[TODO_STATUS.ACTIVE] = 0;
  count[TODO_STATUS.COMPLETED] = 0;
  return {
    todos() {
      return todos;
    },
    get(id) {
      if(todos[id] !== null) {
        return todos[id];
      }
      return null;
    },
    add(todo) {
      todos[todo.id()] = todo;
      count[todo.status()]++;
      return todo;
    },
    remove(todo) {
      if(todos[todo.id()] !== null) {
        delete todos[todo.id()];
        count[todo.status()]--;
        return todo;
      }
      return null;
    },
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
    total() {
      return count[TODO_STATUS.ACTIVE] + count[TODO_STATUS.COMPLETED];
    },
    active() {
      return count[TODO_STATUS.ACTIVE];
    },
    completed() {
      return count[TODO_STATUS.COMPLETED];
    }
  };
};


const xhrRequest = function(request) {
  return new Promise((resolve, reject) => {
    let xhr = new XMLHttpRequest();
    xhr.open(request.method, request.url);
    xhr.addEventListener("readystatechange", function() {
      if(this.readyState === 4 && this.status === 200) {
        let response = JSON.parse(this.responseText);
        resolve(response);
      }
    });
    xhr.addEventListener("error", function() {
      reject({
        "status": "error",
        "error": {
          "httpStatus": this.status,
          "statusText": this.statusText
        }
      });
    })
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    if(request.method === "POST") {
      xhr.send(request.data);
    } else {
      xhr.send();
    }
  });
};

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

const csrfTokenFactory = function() {
  const csrfToken = document.getElementById("csrfmiddlewaretoken");
  return {
    value() {
      return csrfToken.value;
    }
  }
};

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

const databaseFactory = function() { 
  return {
    async all() {
      let url = getOrigin() + "/todos";
      let response = await xhrRequest({
        method: "GET",
        url: url
      });
      let todos = null;
      if(response.status === "success") {
        todos = {};
        for(let id of Object.keys(response.todos)) {
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
    async add(todo) {
      let url = getOrigin() + "/todo";
      let data = "csrfmiddlewaretoken=" + csrfTokenFactory().value();
      data += "&" + todoUrlencodedData(todo);
      let response = await xhrRequest({
        method: "POST",
        url: url,
        data: data
      });
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
    async update(todo) {
      let url = getOrigin() + "/todo/" + todo.id();
      let data = "csrfmiddlewaretoken=" + csrfTokenFactory().value();
      data += "&" + todoUrlencodedData(todo);
      let response = await xhrRequest({
        method: "POST",
        url: url,
        data: data
      });
      let todoRet = null;
      if(response.status === "success") {
        todoRet = processTodoData(response.todo);
      } else {
        console.log("coudn't update todo " + todo.id());
      }
      return todoRet;
    },
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
  checkboxLabel.addEventListener("change", checkboxListener);
  removeBtn.addEventListener("click", removeBtnListener);
  node.updateStatus = function(status) {
    if(status === TODO_STATUS.COMPLETED) {
      checkboxInput.checked = true;
      content.style.textDecoration = "line-through";
    } else {
      checkboxInput.checked = false;
      content.style.textDecoration = "";
    }
  };
  node.updateStatus(todo.status());
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
      node.updateStatus(data.todo.status());
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

async function checkboxListener(ev) {
  ev.preventDefault();
  let node = ev.target.closest(".todo-item");
  let oldTodo = cache.get(node.id);
  let todo = null;
  if(ev.target.checked) {
    todo = oldTodo.updateStatus(TODO_STATUS.COMPLETED);
  } else {
    todo = oldTodo.updateStatus(TODO_STATUS.ACTIVE);
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
