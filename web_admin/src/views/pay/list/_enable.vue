<template>
  <el-dialog title="启用" :visible.sync="visible">
    <el-form label-width="70px" ref="form">

      <el-form-item label="启用">
        <el-checkbox-group v-model="enableIn">
          <el-checkbox :label="paywayEnabled.PC.id">电脑端</el-checkbox>
          <el-checkbox :label="paywayEnabled.MOBILE.id">手机端</el-checkbox>
        </el-checkbox-group>
      </el-form-item>

    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">保存</el-button>
    </div>
  </el-dialog>
</template>


<script>
  import { paywayEnabled, enable } from 'api/pay'

  export default {
    props: {
      info: {}
    },
    data () {
      const enableIn = [];
      if (this.info.enabled & paywayEnabled.PC.id) {
        enableIn.push(paywayEnabled.PC.id);
      }
      if (this.info.enabled & paywayEnabled.MOBILE.id) {
        enableIn.push(paywayEnabled.MOBILE.id);
      }
      return {
        loading: false,
        visible: true,
        paywayEnabled,
        enableIn
      }
    },
    watch: {
      visible (val) {
        if (!val) {
          this.$emit('close', false);
        }
      }
    },
    methods: {
      handleSubmit () {
        this.loading = true;

        let bufStatus = 0;
        this.enableIn.forEach(e => bufStatus |= e);

        enable(this.info.id, bufStatus).then(() => {
          this.loading = false;
          this.handleClose(true);
        }).catch(() => {
          this.loading = false;
        });
      },
      handleClose (success = false) {
        this.visible = false;
        this.$emit('close', success);
      }
    }
  }
</script>


<style scoped>
  .el-form-item {
    margin-bottom: 20px !important;
  }
</style>
