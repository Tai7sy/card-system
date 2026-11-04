<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">数据清理</h2>

      <el-form-item prop="order_clean_date" class="item-container">
        <span>此日期之前订单日志</span><br>
        <el-date-picker size="small" v-model="form.order_clean_date" type="date" align="right"
                        placeholder="请选择日期">
        </el-date-picker>
        <el-button size="small" @click="cleanOrder">清理</el-button>
      </el-form-item>

      <el-form-item prop="fund_record_clean_date" class="item-container">
        <span>此日期之前收支日志</span><br>
        <el-date-picker size="small" v-model="form.fund_record_clean_date" type="date" align="right"
                        placeholder="请选择日期">
        </el-date-picker>
        <el-button size="small" @click="clearFundRecord">清理</el-button>
      </el-form-item>

      <el-form-item prop="logs_clean_date" class="item-container">
        <span>此日期之前登录日志/操作日志</span><br>
        <el-date-picker size="small" v-model="form.logs_clean_date" type="date" align="right"
                        placeholder="请选择日期">
        </el-date-picker>
        <el-button size="small" @click="clearLogs">清理</el-button>
      </el-form-item>


    </el-form>
    
  </el-card>
</template>

<script>
import NProgress from 'nprogress';
import { deleteOrders, deleteFundRecords, deleteLogs } from '@/api/system';
export default {
  components: {},
  data() {
    return {
      loading: false,
      labelWidth: '0px',
      form: {
        order_clean_date: function () {
          const start = new Date();
          start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
          return start;
        }(),
        fund_record_clean_date: function () {
          const start = new Date();
          start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
          return start;
        }(),
        logs_clean_date: function () {
          const start = new Date();
          start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
          return start;
        }()
      },
      formRules: {}
    };
  },
  watch: {
    loading(val, old) {
      if (val) {
        if (!old && !NProgress.isStarted()) NProgress.start();
      } else {
        NProgress.done();
      }
    }
  },
  mounted() {},
  methods: {
    cleanOrder() {
      const date = this.form.order_clean_date.format('yyyy-MM-dd');
      this.$confirm(`是否要删除日期 ${date} 之前订单日志?`).then(() => {
        deleteOrders(date).then(() => {
          this.$notify({
            title: '成功',
            message: `${date} 之前订单日志已删除`,
            type: 'success'
          });
        });
      });
    },
    clearFundRecord() {
      const date = this.form.fund_record_clean_date.format('yyyy-MM-dd');
      this.$confirm(`是否要删除日期 ${date} 之前收支日志?`).then(() => {
        deleteFundRecords(date).then(() => {
          this.$notify({
            title: '成功',
            message: `${date} 之前收支日志已删除`,
            type: 'success'
          });
        });
      });
    },
    clearLogs() {
      const date = this.form.logs_clean_date.format('yyyy-MM-dd');
      this.$confirm(`是否要删除日期 ${date} 之前登录/操作日志?`).then(() => {
        deleteLogs(date).then(() => {
          this.$notify({
            title: '成功',
            message: `${date} 之前登录/操作日志已删除`,
            type: 'success'
          });
        });
      });
    }
  }
};
</script>

