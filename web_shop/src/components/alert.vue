<template>
  <mu-dialog :open="dialog.show" :title="dialog.title" @close="handleCancel" :scrollable="dialog.scrollable">
    <span v-html="dialog.message"></span>
    <mu-text-field v-if="dialog.needInput" type="text" style="width: 100%"
                   v-model="dialog.input" :errorText="errText" @input="errText=''"/>
    <mu-button flat slot="actions" @click="handleCancel"
               v-show="dialog.cancelBtnTitle.length">{{dialog.cancelBtnTitle}}
    </mu-button>
    <mu-button flat slot="actions" primary @click="handleSubmit">{{dialog.submitBtnTitle}}</mu-button>
  </mu-dialog>
</template>

<script>
  export default {
    data () {
      const initData = {
        show: false,
        title: '提示',
        message: '',
        needInput: false,
        input: '',
        inputErrorText: '请先填写',
        cancelBtnTitle: '',
        submitBtnTitle: '确定',
        cancelCallback: null,
        successCallback: null,
        scrollable: false
      };
      return {
        initData,
        dialog: Object.assign({}, initData),
        errText: ''
      }
    },
    created () {
      window.vm.$on('alert', this.show);
    },
    beforeDestroy () {
      window.vm.$off('alert');
    },
    methods: {
      show (options) {
        const initData = Object.assign({}, this.initData);
        this.dialog = Object.assign(initData, options);
        this.dialog.show = true;
      },
      handleCancel () {
        this.dialog.show = false;
        if (this.dialog.cancelCallback) {
          this.dialog.cancelCallback(new Error('cancel'));
        }
      },
      handleSubmit () {
        if (this.dialog.needInput && this.dialog.input.length < 1) {
          this.errText = this.dialog.inputErrorText;
          return;
        }
        this.dialog.show = false;
        if (this.dialog.successCallback) {
          this.dialog.successCallback(this.dialog);
        }
      },
    }
  }
</script>
