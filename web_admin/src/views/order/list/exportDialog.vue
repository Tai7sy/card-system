<template>
  <el-dialog
      title="导出订单"
      :visible.sync="visible"
      @close="$emit('close')"
  >
    <div>

      <p>导出同时改变状态</p>
      <el-select v-model="status" size="small">
        <el-option label="不改变状态" :value="-1"/>
        <el-option label="标记位已完成" :value="SEND_STATUS.CARD_SUCCESS.id"/>
        <el-option label="标记位正在处理" :value="SEND_STATUS.CARD_PROCESSING.id"/>
      </el-select>

      <div v-if="status === SEND_STATUS.CARD_SUCCESS.id">
        <p>请输入发货卡密(当前导出订单都将使用此相同卡密)</p>
        <el-input v-model="card" autocomplete="false" size="small"/>
      </div>

    </div>
    <div slot="footer" class="dialog-footer">
      <el-button @click="$emit('close')">取消</el-button>
      <el-button type="primary" @click="handleSubmit">确认导出</el-button>
    </div>
  </el-dialog>
</template>

<script>
  import { SEND_STATUS } from '~/api/order'

  export default {
    name: "exportDialog",
    data () {
      return {
        visible: true,
        SEND_STATUS,
        status: -1,
        card: ''
      }
    },
    methods: {
      handleSubmit () {
        this.$emit('export', {
          status: this.status,
          card: this.card
        });
      }
    }
  }
</script>

<style scoped>

</style>
