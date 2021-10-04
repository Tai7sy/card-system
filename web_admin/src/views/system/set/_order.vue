<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">订单设置</h2>

      <el-form-item prop="order_pay_timeout_minutes" class="item-container">
        <span>订单支付超时(分钟)</span>
        <el-input type="number" placeholder="请输入订单支付超时分钟" v-model="form.order_pay_timeout_minutes"/>
        <span class="tip">支付超时后, 未支付订单将不再允许支付</span>
      </el-form-item>

      <el-form-item prop="order_query_day" class="item-container">
        <span>订单查询最长天数</span>
        <el-input type="number" placeholder="请输入订单查询最长天数" v-model="form.order_query_day"/>
        <span class="tip">买家将无法查询超过此天数的订单</span>
      </el-form-item>

      <el-form-item class="item-container">
        <el-switch
            v-model="form.order_query_password_open"
            inactive-text="开启订单查询密码"
            :active-value="1"
            :inactive-value="0">
        </el-switch>
      </el-form-item>

      <el-form-item class="item-container">
        <el-switch
          v-model="form.order_clean_unpay_open"
          inactive-text="清理未支付订单"
          :active-value="1"
          :inactive-value="0">
        </el-switch>
      </el-form-item>

      <el-form-item v-show="form.order_clean_unpay_open" prop="order_clean_unpay_day" class="item-container"
                    :rules="{ required: true, message: '请输入未支付订单保留天数', trigger: 'blur' }">
        <span>未支付订单保留天数</span>
        <el-input type="number" placeholder="请输入未支付订单保留天数" v-model="form.order_clean_unpay_day"/>
        <span class="tip">超过此天数的未支付订单将被删除</span>
      </el-form-item>

      <div v-show="form.order_clean_unpay_open" style="font-size: 12px">
        <span>注意: 本功能需要开启定时任务</span>
        <code style="display: block"> * * * * * php 安装目录/artisan schedule:run;</code><br>
        你也可以点此&nbsp;<el-button size="mini" @click="clearOrder">手动清理</el-button>
      </div>


    </el-form>
    <div class="text-center" style="margin-top: 24px">
      <el-button @click="handleResetForm">刷新</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">保存更改</el-button>
    </div>
  </el-card>
</template>

<script>
  import NProgress from 'nprogress';
  import { order, orderClean } from '~admin/api/system';

  export default {
    components: {},
    data () {
      return {
        loading: false,
        labelWidth: '0px', // this.$store.state.app.isMobile ? '70px' : '90px',
        form: {
          order_query_day: 30,
          order_pay_timeout_minutes: 5,
          order_query_password_open: 0,
          order_clean_unpay_open: 0,
          order_clean_unpay_day: 7
        },
        formRules: {
          order_query_day: [
            { required: true, message: '请输入订单查询最长天数', trigger: 'blur' }
          ],
          order_pay_timeout_minutes: [
            { required: true, message: '请输入订单支付超时分钟', trigger: 'blur' }
          ],
        }
      }
    },
    watch: {
      loading (val, old) {
        if (val) {
          if (!old && !NProgress.isStarted()) NProgress.start();
        } else {
          NProgress.done();
        }
      }
    },
    mounted () {
      this.getSet();
    },
    methods: {
      getSet () {
        this.loading = true;
        order().then(res => {
          Object.assign(this.form, res.data);
          this.loading = false;
        });
      },
      handleResetForm () {
        this.getSet();
      },
      handleSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;
          order(this.form).then(() => {
            this.loading = false;
            this.$notify({ title: '操作成功', message: '配置保存成功', type: 'success' });
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      clearOrder () {
        this.$confirm(`是否要删除 ${this.form.order_clean_unpay_day}天 前未支付订单?`).then(() => {
          orderClean(this.form.order_clean_unpay_day).then(() => {
            this.$notify({
              title: '成功',
              message: `${this.form.order_clean_unpay_day}天 前未支付订单已删除`,
              type: 'success'
            })
          })
        });
      }
    }
  }
</script>

