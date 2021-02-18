<div class="contents">
    <?= $_csrf() ?>
    <div class="todo-input-section">
        <div class="todo-input-wrapper">
            <div class="expansion-symbol" style="visibility: hidden;">
                <span class="expansion-symbol-content">﹀</span>
            </div>
            <div class="todo-form">
                <input class="todo-form-content" type="text" name="todo" id="todo" placeholder="What needs to be done?">
            </div>
        </div>
    </div>
    <div class="todo-item-section" hidden>
        <div class="todo-item-list">
        </div>
        <div class="todo-item-functions">
            <div class="todo-active-item-count">2 items left</div>
            <div class="all-btn todo-function-btn">All</div>
            <div class="active-btn todo-function-btn">Active</div>
            <div class="completed-btn todo-function-btn">Completed</div>
            <div class="clear-completed-btn" style="visibility: hidden;">Clear Completed</div>
        </div>
    </div>
    <div id="templates" hidden>
        <div class="todo-item" id="todo-item-template">
            <div class="todo-item-wrapper">
                <label class="todo-checkbox">
                    <input type="checkbox">
                    <span></span>
                </label>
                <div class="todo-item-content-parent">
                    <div class="todo-item-content">
                        One
                    </div>
                </div>
                <div class="todo-item-remove">
                </div>
            </div>
        </div>
    </div>
</div>