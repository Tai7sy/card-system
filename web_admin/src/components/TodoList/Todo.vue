<template>
    <li class="todo" :class="{ completed: todo.status == status.completed, editing: editing }">
        <div class="view">
            <input class="toggle"
                   type="checkbox"
                   v-model="checked"
                   @change="toggleTodo(todo)" title="完成">
            <label v-text="todo.text" @dblclick="editing = true"></label>
            <button class="destroy" @click="deleteTodo( todo )"></button>
        </div>
        <input class="edit"
               v-show="editing"
               v-focus="editing"
               :value="todo.text"
               @keyup.enter="doEdit"
               @keyup.esc="cancelEdit"
               @blur="doEdit" title="编辑">
    </li>
</template>

<script>
    import { status } from 'api/todo'

    export default {
        name: 'Todo',
        props: ['todo'],
        data() {
            return {
                editing: false,
                status,
                checked: this.todo.status === status.completed
            }
        },
        directives: {
            focus(el, { value }, { context }) {
                if (value) {
                    context.$nextTick(() => {
                        el.focus()
                    })
                }
            }
        },
        watch: {
            // 组件存在复用情况 属性要重新计算
            todo() {
                this.editing = false;
                this.checked = this.todo.status === status.completed;
            }
        },
        methods: {
            deleteTodo(todo) {
                this.$emit('deleteTodo', todo)
            },
            editTodo({ todo, value }) {
                this.$emit('editTodo', { todo, value })
            },
            toggleTodo(todo) {
                this.$emit('toggleTodo', todo)
            },
            doEdit(e) {
                const value = e.target.value.trim();
                const { todo } = this;
                if (!value) {
                    this.deleteTodo({
                        todo
                    })
                } else if (this.editing) {
                    this.editTodo({
                        todo,
                        value
                    });
                    this.editing = false
                }
            },
            cancelEdit(e) {
                e.target.value = this.todo.text;
                this.editing = false
            }
        }
    }
</script>
