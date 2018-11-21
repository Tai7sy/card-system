<template>
    <section class="todoapp">
        <!-- header -->
        <header class="header">
            <input class="new-todo" autocomplete="off" placeholder="点击添加" v-model="addText">
        </header>
        <!-- main section -->
        <section class="main">
            <input class="todo-add-btn" id="todo-add-btn" type="button" @click="addTodo">
            <label for="todo-add-btn" @click="addTodo"></label>
            <ul class="todo-list">
                <todo @toggleTodo='toggleTodo' @editTodo='editTodo' @deleteTodo='deleteTodo'
                      v-for="(todo, index) in filteredTodos" :key="index" :todo="todo"></todo>
                <li class="todo" v-show="!filteredTodos.length">
                    <div class="view"><label>空空如也 ~ ~</label></div>
                </li>
            </ul>
        </section>
        <!-- footer -->
        <footer class="footer" v-show="todos.length">
            <span class="todo-count">
                <strong>{{ remaining }}</strong>&nbsp;待办
            </span>
            <ul class="filters">
                <li v-for="(val, key) in filters" :key="key">
                    <a :class="{ selected: visibility === key }"
                       @click.prevent="visibility = key">{{ key | capitalize }}</a>
                </li>
            </ul>
            <button class="clear-completed" v-show="todos.length > remaining" @click="clearCompleted">
                清空已完成
            </button>
        </footer>

        <div class="el-loading-mask" v-show="loading">
            <div class="el-loading-spinner">
                <svg viewBox="25 25 50 50" class="circular">
                    <circle cx="50" cy="50" r="20" fill="none" class="path"></circle>
                </svg>
                <!--p class="el-loading-text">ing...</p-->
            </div>
        </div>
    </section>
</template>

<script>
    import Todo from './Todo.vue'
    import { status, todo_get, todo_add, todo_edit, todo_do, todo_delete, todo_delete_completed } from 'api/todo'


    const filters = {
        all: todos => todos,
        active: todos => todos.filter(todo => todo.status === status.active),
        completed: todos => todos.filter(todo => todo.status === status.completed)
    };

    export default {
        components: { Todo },
        data() {
            return {
                addText: '',
                visibility: 'all',
                filters,
                todos: [],
                loading: false
            }
        },
        computed: {
            filteredTodos() {
                return filters[this.visibility](this.todos)
            },
            remaining() {
                return this.todos.filter(todo => todo.status === status.active).length
            }
        },
        mounted() {
            this.getTodos();
        },
        methods: {
            getTodos() {
                this.loading = true;
                todo_get().then(res => {
                    this.todos = res.data;
                    this.loading = false;
                }).catch(() => {
                    this.loading = false;
                });
            },
            addTodo() {
                const text = this.addText;
                this.addText = '';
                if (!text.trim()) return;

                this.loading = true;
                todo_add({ text }).then(res => {
                    this.todos.push({
                        id: res.data.id,
                        text,
                        status: status.active
                    });
                    this.loading = false;
                }).catch(() => {
                    this.loading = false;
                });
            },
            toggleTodo(val) {
                this.loading = true;
                const toStatus = val.status === status.active ? status.completed : status.active;
                todo_do({ id: val.id, status: toStatus }).then(() => {
                    val.status = toStatus;
                    this.loading = false;
                }).catch(() => {
                    this.loading = false;
                });
            },
            deleteTodo(todo) {
                this.loading = true;
                todo_delete({ id: todo.id }).then(() => {
                    this.todos.splice(this.todos.indexOf(todo), 1);
                    this.loading = false;
                }).catch(() => {
                    this.loading = false;
                });
            },
            editTodo({ todo, value }) {
                this.loading = true;
                todo_edit({ id: todo.id, text: value }).then(() => {
                    todo.text = value;
                    this.loading = false;
                }).catch(() => {
                    this.loading = false;
                });
            },
            clearCompleted() {
                this.loading = true;
                todo_delete_completed().then(() => {
                    this.todos = this.todos.filter(todo => todo.status === status.active);
                    this.loading = false;
                }).catch(() => {
                    this.loading = false;
                });
            }
        },
        filters: {
            capitalize: s => s.charAt(0).toUpperCase() + s.slice(1)
        }
    }
</script>

<style lang="scss">
    @import './index.scss';
</style>
