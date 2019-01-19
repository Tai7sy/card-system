<template>
  <div class="login-container">
    <el-form :model="form" :rules="formRules" ref="form" @submit.native.prevent="handleLogin"
             label-position="left" label-width="0px" class="login-form">
      <h3 class="title">后台登录</h3>
      <el-form-item prop="email">
        <el-input type="email" v-model="form.email" placeholder="邮箱"/>
      </el-form-item>
      <el-form-item prop="password">
        <el-input type="password" v-model="form.password" placeholder="密码"/>
      </el-form-item>
      <el-checkbox v-model="form.remember" checked class="remember">记住密码</el-checkbox>
      <el-form-item style="width:100%;">
        <div id="embed-captcha"></div>
        <button id="bind-geetest" style="display: none" @click.prevent="handleLoginSubmit"></button>
        <el-button type="primary" style="width:100%;" @click.native.prevent="handleLogin"
                   :loading="loading || geetestLoading">{{ geetestLoading ? '加载中' :'登录' }}
        </el-button>
        <!--<el-button @click.native.prevent="handleReset2">重置</el-button>-->
      </el-form-item>
    </el-form>

  </div>

</template>

<script>
  import { validateEmail } from '../../utils/validate';
  import { getVerify } from '@/api/login'
  // import NProgress from 'nprogress'
  export default {
    data () {
      const vcodeConfig = window.config && window.config.vcode ? window.config.vcode : {};
      return {
        loading: false,
        vcode: vcodeConfig,
        geetestLoading: false,
        capture: null,
        validate: false,
        form: {
          email: this.$route.query.email,
          password: '',
          remember: true
        },
        formRules: {
          email: [
            { required: true, message: '请输入邮箱', trigger: 'blur' },
            {
              validator: (rule, value, callback) => {
                if (!validateEmail(value)) {
                  callback(new Error('请输入正确的邮箱'));
                } else {
                  callback();
                }
              }
            }
          ],
          password: [
            { required: true, message: '请输入密码', trigger: 'blur' }
            // { validator: xx }
          ]
        }
      };
    },
    mounted () {
      this.verticalCenter();
      window.addEventListener('resize', this.verticalCenter);

      if (this.vcode.geetest && +this.vcode.login === 1) {
        if (this.vcode.geetest.gt !== false) {
          this.renderGeeCaptcha(this.vcode.geetest);
          this.vcode.geetest.gt = false;
        } else {
          this.handleReInitCapture();
        }
      } else {
        console.log('login - disable Captcha');
        this.renderGeeCaptcha(null);
      }
    },
    destroyed () {
      window.removeEventListener('resize', this.verticalCenter)
    },
    methods: {
      verticalCenter () {
        const form = $(this.$refs['form'].$el);
        form.css({
          position: 'absolute',
          left: ($(window).width() - form.outerWidth()) / 2,
          top: ($(window).height() - form.outerHeight()) / 2.5
        });
      },
      handleReset2 () {
        this.$refs['form'].resetFields();
      },
      handleLogin () {
        if (!this.capture) {
          return this.$alert('页面正在加载，请稍后');
        }
        this.$refs['form'].validate(valid => {
          if (valid) {
            $('#bind-geetest').click();
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      },
      handleLoginSubmit () {
        if (!this.validate) {
          return this.$alert('请先完成验证');
        }

        this.loading = true;
        const postData = Object.assign({}, this.form);
        if (typeof this.validate === 'object') {
          console.log(this.validate);
          postData.gt_c = this.validate.geetest_challenge;
          postData.gt_v = this.validate.geetest_validate;
          postData.gt_s = this.validate.geetest_seccode;
        }
        this.$store.dispatch('Login', postData).then(data => {
          this.$store.dispatch('GenerateRoutes', data).then(() => {
            this.$router.addRoutes(this.$store.getters.addRouters);
            console.log('router.addRoutes ok');
            this.loading = false;
            if (this.$route.query.to) {
              this.$router.push({ path: this.$route.query.to });
            } else {
              this.$router.push({ path: '/admin/' });
            }
          });
        }).catch(() => {
          // this.handleReInitCapture();
        }).finally(() => {
          this.loading = false;
        });
      },
      handleReInitCapture () {
        console.log('login - handleReInitCapture');
        this.geetestLoading = true;
        this.validate = false;
        getVerify().then(ret => {
          const data = ret.data;
          if (!data.geetest) {
            if (this.vcode.geetest && this.vcode.login === 1) {
              this.$alert('获取验证码失败，请刷新页面重试', '获取验证码失败');
            }
            return;
          }
          if (data.driver === 'geetest') {
            this.renderGeeCaptcha(data.geetest);
          } else {
            // waiting...
            this.renderGeeCaptcha(null);
          }
        }).catch(() => {
          this.$alert('获取验证码失败，请刷新页面重试', '获取验证码失败');
        });
      },
      renderGeeCaptcha (geetest) {
        if (geetest) {
          try {
            this.geetestLoading = true;
            window.initGeetest({
              gt: geetest.gt,
              challenge: geetest.challenge,
              product: 'popup', // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
              offline: !geetest.success // 表示用户后台检测极验服务器是否宕机，与SDK配合，用户一般不需要关注
            }, captchaObj => {
              captchaObj.onReady(() => {
                this.geetestLoading = false;
              });
              captchaObj.onError(() => {
                // 出错啦，可以提醒用户稍后进行重试
                this.$alert('页面超时，请刷新页面重试', '提示').then(() => {
                  location.reload(true);
                });
              });
              captchaObj.onSuccess(() => {
                this.validate = captchaObj.getValidate();
                // this.handleLoginSubmit();
              });
              captchaObj.appendTo('#embed-captcha');
              if (captchaObj.bindOn) captchaObj.bindOn('#bind-geetest');
              // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html

              this.capture = captchaObj;
            });
          } catch (e) {
            this.$alert('登录验证码初始化失败:' + e);

          }
        } else {
          this.capture = { verify: false };
          this.geetestLoading = false;
          this.validate = true;
        }
      },
    }
  }
</script>

<style lang="scss" scoped>

  .login-container {
    height: 100%;
    // background: url(../../assets/bkg/blue-mountain.jpg) no-repeat 0 0;
    background-size: 100% 1000px;
  }

  .login-form {
    -webkit-border-radius: 5px;
    border-radius: 5px;
    -moz-border-radius: 5px;
    background-clip: padding-box;
    //margin: 30% auto;
    width: 350px;
    padding: 35px 35px 15px 35px;
    background: #fff;
    border: 1px solid #eaeaea;
    box-shadow: 0 15px 30px 0 rgba(0, 0, 0, .2);
    .title {
      margin: 0 auto 40px auto;
      text-align: center;
      color: #505458;
    }
    .remember {
      margin: 0 0 28px 0;
    }
  }

  .forget-btn {
    color: #606266;
    font-size: 14px;
    cursor: pointer;
    float: right
  }

  .reg-btn {
    color: #606266;
    font-size: 14px;
    text-align: center;
    padding-top: 16px;
    a {
      color: #20A0FF;
    }
  }

  @media only screen and (max-width: 350px) {
    .login-form, .reg-btn {
      width: 100%;
    }
  }
</style>
