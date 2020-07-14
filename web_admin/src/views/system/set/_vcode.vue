<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">验证码驱动</h2>

      <el-form-item class="item-container">
        <span>驱动</span>
        <el-radio-group v-model="form.vcode_driver">
          <el-radio label="code">普通验证码</el-radio>
          <el-radio label="geetest">Geetest</el-radio>
        </el-radio-group>
      </el-form-item>

      <div v-if="form.vcode_driver === 'geetest'">
        <el-form-item prop="vcode_geetest_id" class="item-container">
          <span>GEETEST_ID</span>
          <el-input type="text" placeholder="GEETEST_ID" v-model="form.vcode_geetest_id"/>
        </el-form-item>

        <el-form-item prop="vcode_geetest_key" class="item-container">
          <span>GEETEST_KEY</span>
          <el-input type="text" placeholder="GEETEST_KEY" v-model="form.vcode_geetest_key"/>
        </el-form-item>
      </div>

      <h2 class="title" style="margin-top: 48px">后台验证码</h2>

      <!--el-form-item class="item-container">
        <span>驱动</span>
        <el-radio-group v-model="form.vcode_driver">
          <el-radio label="geetest"/>
        </el-radio-group>
      </el-form-item-->

      <el-form-item class="item-container">
        <el-switch
          v-model="form.vcode_login_admin"
          inactive-text="后台登录验证码"
          :active-value="1"
          :inactive-value="0">
        </el-switch>
      </el-form-item>

      <h2 class="title" style="margin-top: 48px">店铺验证码</h2>
      <el-form-item class="item-container">
        <el-switch
          v-model="form.vcode_shop_buy"
          inactive-text="下单"
          :active-value="1"
          :inactive-value="0">
        </el-switch>
      </el-form-item>
      <el-form-item class="item-container">
        <el-switch
          v-model="form.vcode_shop_search"
          inactive-text="查询订单"
          :active-value="1"
          :inactive-value="0">
        </el-switch>
      </el-form-item>

    </el-form>
    <div class="text-center" style="margin-top: 24px">
      <el-button @click="handleResetForm">刷新</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">保存更改</el-button>
    </div>
  </el-card>
</template>

<script>
  import NProgress from 'nprogress';
  import { vcode } from '~admin/api/system';

  export default {
    components: {},
    data () {
      return {
        loading: false,
        labelWidth: '0px', // this.$store.state.app.isMobile ? '70px' : '90px',
        form: {
          vcode_driver: 'code',
          vcode_geetest_id: '',
          vcode_geetest_key: '',
          vcode_login_admin: 0,
          vcode_shop_buy: 0,
          vcode_shop_search: 0
        },
        formRules: {}
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
        vcode().then(res => {
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
          vcode(this.form).then(() => {
            this.loading = false;
            this.$notify({ title: '操作成功', message: '配置保存成功', type: 'success' });
          }).catch(() => {
            this.loading = false;
          });
        });
      }
    }
  }
</script>


