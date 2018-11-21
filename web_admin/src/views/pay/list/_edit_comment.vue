<template>
  <el-dialog title="编辑备注" :visible.sync="visible">
    <el-form label-width="70px" ref="form">

      <el-form-item label="备注">
        <el-input type="textarea" :autosize="{ minRows: 3, maxRows: 10}" :maxlength="100"
                  placeholder="请输入备注" v-model="comment">
        </el-input>
      </el-form-item>

    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">保存</el-button>
    </div>
  </el-dialog>
</template>


<script>
  import { edit_comment } from 'api/pay'

  export default {
    props: {
      info: {}
    },
    data () {
      return {
        loading: false,
        visible: true,
        comment: this.info.comment,
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
        edit_comment(this.info.id, this.comment).then(() => {
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
