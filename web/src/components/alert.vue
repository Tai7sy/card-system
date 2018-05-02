<template>
  <mu-dialog :open="dialog.show" :title="dialog.title" @close="dialog.show=false" scrollable>
    <span v-html="dialog.msg"></span>
    <mu-flat-button slot="actions" @click="dialog.show=false" label="取消" v-show="dialog.hasCancel"/>
    <mu-flat-button slot="actions" primary @click="alertClicked" label="确定"/>
  </mu-dialog>
</template>

<script>
  export default {
    data () {
      return {
        dialog: {
          show: false,
          title: '提示',
          msg: '',
          callback: null,
          hasCancel: false
        }
      }
    },
    created () {
      window.vm.$on('alert', alertInfo => {
        this.alert(alertInfo.msg, alertInfo.title, alertInfo.callback, alertInfo.hasCancel);
      });
    },
    methods: {
      alert (msg, title, callback, hasCancel) {
        this.dialog.msg = msg;
        this.dialog.title = (title || '提示');
        this.dialog.show = true;
        this.dialog.hasCancel = hasCancel;
        this.dialog.callback = callback;
      },
      alertClicked () {
        this.dialog.show = false;
        this.dialog.callback && this.dialog.callback();
      },
    }
  }
</script>
