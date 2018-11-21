<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">邮件驱动</h2>
      <el-form-item class="item-container">
        <span>驱动</span>
        <el-radio-group v-model="form.mail_driver">
          <el-radio label="smtp"/>
          <el-radio label="sendcloud"/>
        </el-radio-group>
      </el-form-item>

      <div v-if="form.mail_driver === 'smtp'">
        <el-form-item prop="mail_smtp_host" class="item-container">
          <span>服务器地址</span>
          <el-input type="text" placeholder="服务器地址" v-model="form.mail_smtp_host"/>
        </el-form-item>

        <el-form-item prop="mail_smtp_port" class="item-container">
          <span>服务器端口</span>
          <el-input type="text" placeholder="25(null)/465(ssl)/587(tls)" v-model="form.mail_smtp_port"/>
        </el-form-item>

        <el-form-item prop="mail_smtp_username" class="item-container">
          <span>用户名</span>
          <el-input type="text" placeholder="用户名" v-model="form.mail_smtp_username"/>
        </el-form-item>

        <el-form-item prop="mail_smtp_password" class="item-container">
          <span>密码</span>
          <el-input type="text" placeholder="密码" v-model="form.mail_smtp_password"/>
        </el-form-item>

        <el-form-item prop="mail_smtp_from_address" class="item-container">
          <span>发件人地址</span>
          <el-input type="text" placeholder="发件人地址" v-model="form.mail_smtp_from_address"/>
        </el-form-item>

        <el-form-item prop="mail_smtp_from_name" class="item-container">
          <span>发件人姓名</span>
          <el-input type="text" placeholder="发件人姓名" v-model="form.mail_smtp_from_name"/>
        </el-form-item>

        <el-form-item prop="mail_smtp_encryption" class="item-container">
          <span>加密</span>
          <el-input type="text" placeholder="null/ssl/tls" v-model="form.mail_smtp_encryption"/>
        </el-form-item>

      </div>
      <div v-if="form.mail_driver === 'sendcloud'">
        <el-form-item prop="sendcloud_user" class="item-container">
          <span>User</span>
          <el-input type="text" placeholder="User" v-model="form.sendcloud_user"/>
        </el-form-item>

        <el-form-item prop="sendcloud_key" class="item-container">
          <span>Key</span>
          <el-input type="text" placeholder="Key" v-model="form.sendcloud_key"/>
        </el-form-item>
      </div>

      <h2 class="title" style="margin-top: 48px">测试发送</h2>
      <el-form-item class="item-container">
        <el-input placeholder="请输入邮箱" v-model="testEmail"/>
      </el-form-item>
      <el-form-item class="item-container">
        <el-button size="small" @click="handleEmailTest" :loading="sending">{{sending ? '发送中':'发送'}}</el-button>
      </el-form-item>

      <h2 class="title" style="margin-top: 48px">邮件发送选项</h2>
      <el-form-item class="item-container">
        <el-switch
          v-model="form.mail_send_order"
          inactive-text="发送卡密到买家邮箱"
          :active-value="1"
          :inactive-value="0">
        </el-switch>
        <span class="tip" style="display: block; margin-top: -12px;">注意：只有当联系方式为邮箱时才会发送</span>
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
  import { email, emailTest } from 'api/system';

  export default {
    components: {},
    data () {
      return {
        loading: false,
        labelWidth: '0px', // this.$store.state.app.isMobile ? '70px' : '90px',
        form: {
          mail_driver: '',
          mail_smtp_host: '',
          mail_smtp_port: '',
          mail_smtp_username: '',
          mail_smtp_password: '',
          mail_smtp_from_address: '',
          mail_smtp_from_name: '',
          mail_smtp_encryption: '',
          sendcloud_user: '',
          sendcloud_key: '',
          mail_send_order: 0,
        },
        formRules: {},
        testEmail: '',
        sending: false,
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
      this.testEmail = this.$store.state.user.userInfo.email;
    },
    methods: {
      getSet () {
        this.loading = true;
        email().then(res => {
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
          email(this.form).then(() => {
            this.loading = false;
            this.$notify({ title: '操作成功', message: '配置保存成功', type: 'success' });
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleEmailTest () {
        this.sending = true;
        emailTest(this.testEmail).then(e => {
          this.sending = false;
          this.$alert(e.data, '发送成功', {
            type: 'success'
          });
        }).finally(() => {
          this.sending = false;
        })
      }
    }
  }
</script>


