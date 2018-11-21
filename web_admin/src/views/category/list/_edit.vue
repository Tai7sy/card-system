<template>
  <el-dialog title="编辑分类" :visible.sync="visible">

    <category-add ref="editBox" :in-box="true" :info="info" @submit="handleClose(true)" :loading.sync="loading"></category-add>

    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">确认编辑
      </el-button>
    </div>
  </el-dialog>
</template>

<script>
  import categoryAdd from '../add/index'

  export default {
    props: {
      info: {}
    },
    components: { categoryAdd },
    data () {
      return {
        loading: false,
        visible: true,
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
        this.$refs['editBox'].handleSubmit();
      },
      handleClose (success = false) {
        this.visible = false;
        this.$emit('close', success);
      }
    }
  }
</script>
