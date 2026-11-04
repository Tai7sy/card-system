<template>
  <div class="el-input-group el-input-group--prepend">

    <mu-text-field
        class="el-input__inner"
        :type="type"
        :disabled="disabled"
        :readonly="readonly"
        :placeholder="placeholder"
        ref="input"
        v-model="inputValue"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
        @change="handleChange"
    />

    <!-- 后置元素 -->
    <div class="el-input-group__append" v-if="$slots.append">
      <slot name="append"></slot>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      title: {},
      placeholder: {},
      disabled: {
        default: false
      },
      readonly: {
        default: false
      },
      value:{
      }
    },
    data() {
      return {
        type: 'text',
        focused: false,
        inputValue: this.value,
      }
    },
    watch: {
      value(val){
        this.inputValue = val;
      }
    },
    methods:{
      handleInput(value) {
        this.$emit('input', value);
      },
      handleChange(value) {
        this.$emit('change', value);
      },
      handleFocus(event) {
        this.focused = true;
        this.$emit('focus', event);
      },
      handleBlur(event) {
        this.focused = false;
        this.$emit('blur', event);
      },
      focus(){
        this.$refs.input.focus();
      }
    }
  }
</script>

<style lang="scss">
  .el-input-group {
    line-height: normal;
    display: inline-table;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0
  }

  .el-input-group--append .el-input__inner, .el-input-group__prepend {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
  }

  .el-input-group>.el-input__inner {
    vertical-align: middle;
    display: table-cell;
    padding-bottom: 0;
  }

  .el-input-group__append {
    background-color: #f5f7fa;
    color: #909399;
    vertical-align: middle;
    display: table-cell;
    position: relative;
    border: 1px solid #dcdfe6;
    border-radius: 4px;
    padding: 0 20px;
    width: 1px;
    white-space: nowrap;
  }
</style>