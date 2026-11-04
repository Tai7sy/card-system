<template>
  <el-dialog title="导出卡密" :visible.sync="visible" :close-on-click-modal="false">
    <el-form ref="form" :model="form" label-position="top">

      <el-form-item label="导出范围">
        <el-radio-group v-model="form.range" size="small">
          <el-radio label="all">全部({{list.total}})</el-radio>
          <el-radio label="current_page">当前页</el-radio>
          <el-radio label="limit">指定数量</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="导出数量" v-if="form.range === 'limit'">
        <el-input type="number" placeholder="请输入导出数量" v-model.number="form.limit">
        </el-input>
      </el-form-item>

      <el-form-item label="是否删除">
        <el-radio-group v-model="form.is_delete" size="small">
          <el-radio :label="0">仅导出不删除</el-radio>
          <el-radio :label="1">导出并删除卡密</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="商品名称">
        <el-radio-group v-model="form.is_product_name" size="small">
          <el-radio :label="1">导出商品名</el-radio>
          <el-radio :label="0">不需要商品名</el-radio>
        </el-radio-group>
      </el-form-item>


      <el-form-item label="导出格式">
        <el-radio-group v-model="form.format" size="small">
          <el-radio label="txt">TXT</el-radio>
          <el-radio label="csv">CSV</el-radio>
        </el-radio-group>
      </el-form-item>

    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit">确认导出</el-button>
    </div>
  </el-dialog>
</template>

<script>
  import { param } from '~/utils/helper'

  export default {
    name: 'card-export-dialog',
    props: {
      where: {},
      list: {}
    },
    data() {
      return {
        visible: true,
        form: {
          range: 'all',
          limit: 10,
          is_delete: 0,
          is_product_name: 1,
          format: 'txt',
        }
      }
    },
    watch: {
      visible(val) {
        if (!val) {
          this.$emit('close', false);
        }
      }
    },
    methods: {
      handleSubmit() {
        window.open(window.API_URL + 'card/export?' + param({
          ...this.where,
          ...this.form,
        }) + '&token=' + encodeURIComponent(this.$store.state.user.token), '_blank');
      },
      handleClose() {
        this.visible = false;
        this.$emit('close');
      }
    }
  }
</script>
