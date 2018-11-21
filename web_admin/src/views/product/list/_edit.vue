<template>
  <el-dialog title="编辑商品" :visible.sync="visible">

    <product-add ref="editBox" :in-box="true" :info="info" @submit="onSubmit" :loading.sync="loading"></product-add>

    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">确认编辑
      </el-button>
    </div>
  </el-dialog>
</template>

<script>
  import productAdd from '../add/index'

  export default {
    props: {
      info: {}
    },
    components: { productAdd },
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
      onSubmit (ret = false) {
        this.visible = false;
        this.$emit('close', ret);
      }
    }
  }
</script>
