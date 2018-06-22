<template>
  <mu-dialog :open="true" title="启用" @close="editCallback(false)">
    <div style="margin: 12px 0">
      <span style="display: inline-block;line-height: 24px; vertical-align:top;">启用:&nbsp;</span>
      <mu-checkbox name="enableIn" label="电脑端" nativeValue="1" v-model="enableIn" style="vertical-align:top;"/>
      <mu-checkbox name="enableIn" label="手机端" nativeValue="2" v-model="enableIn" style="vertical-align:top;"/>
    </div>
    <mu-flat-button slot="actions" @click="editCallback(false)" primary label="取消"/>
    <mu-flat-button slot="actions" primary @click="handleEditSubmit" label="确定"/>
  </mu-dialog>
</template>

<script>
  export default {
    props: {
      info: {}
    },
    data () {
      const enableIn = []
      if (this.info.enabled & 1) {
        enableIn.push(1);
      }
      if (this.info.enabled & 2) {
        enableIn.push(2);
      }
      return {
        enableIn
      }
    },
    methods: {
      editCallback (ret) {
        this.$emit('ret', ret)
      },
      handleEditSubmit () {
        let bufStatus = 0;
        this.enableIn.forEach(e => bufStatus |= e);

        this.$http.post('admin/pay/enabled', {
          id: this.info.id,
          enabled: bufStatus
        }).then(() => {
          this.editCallback(true)
        })
      }
    }
  }
</script>
