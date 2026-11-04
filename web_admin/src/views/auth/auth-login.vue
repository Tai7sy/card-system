<template>
  <el-form :model="form"
           :rules="formRules"
           ref="form"
           @submit.native.prevent="handleLogin"
           label-position="left"
           label-width="0px"
           class="login-form">
    <div class="title">
      <h2>后台登录</h2>
    </div>
    <el-form-item prop="email">
      <el-input type="email" v-model="form.email" placeholder="电子邮箱"/>
    </el-form-item>
    <el-form-item prop="password">
      <el-input type="password" v-model="form.password" placeholder="密码"/>
    </el-form-item>

    <el-form-item prop="captcha" v-if="captcha.scene.auth.login">
      <captcha ref="captcha" v-model="form.captcha" :driver="captcha.driver" :config="captcha.config"/>
    </el-form-item>

    <el-checkbox v-model="form.remember" class="remember">使我保持登录状态</el-checkbox>
    <el-form-item style="margin: 24px 0 0">
      <el-button type="primary" class="auth-button" native-type="submit" @click.native.prevent="handleLogin"
                 :loading="loading || captcha.loading">{{ captcha.loading ? '加载中' :'登录' }}
      </el-button>
    </el-form-item>
  </el-form>
</template>

<script>
  import { validateEmail } from '~/utils/validate';
  import Captcha from "~/components/Captcha";

  export default {
    components: { Captcha },
    data() {
      return {
        loading: false,
        captcha: {
          loading: false,
          ...window.config.captcha
        },
        form: {
          email: this.$route.query.email,
          password: '',
          captcha: {},
          remember: false
        },
        formRules: {
          email: [
            { required: true, message: '请输入电子邮箱', trigger: 'blur' },
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
    mounted() {
      this.refreshCaptcha();
    },
    methods: {
      refreshCaptcha() {
        if (this.captcha.scene.auth.login) {
          this.captcha.loading = true;
          this.$refs.captcha.refresh().then(() => {
            this.captcha.loading = false;
          }).catch(e => {
            this.$alert('验证码加载失败，请刷新重试<br>' + e.message, '错误', {
              dangerouslyUseHTMLString: true
            });
          })
        }
      },
      async handleLogin() {
        try {
          await this.$refs['form'].validate();
        } catch (_) {
          // 获取表单验证失败原因并自动设置焦点
          for (const field of this.$refs.form.fields) {
            if (field.validateState === 'error') {
              this.$alert(field.validateMessage, {
                type: 'error'
              }).finally(() => {
                field.$children[1].focus();
              });
              break;
            }
          }
          return;
        }

        // 如果开启了验证码
        if (this.captcha.scene.auth.login) {
          try {
            await this.$refs.captcha.verify();
          } catch (e) {
            this.$alert(e.message, {
              type: 'error',
              dangerouslyUseHTMLString: true
            }).finally(() => {
              this.$refs.captcha.focus()
            });
            return;
          }
        }

        this.loading = true;
        try {
          const response = await this.$store.dispatch('Login', this.form);
          this.loading = false;
          this.$store.dispatch('GenerateRoutes', response).then(() => {
            this.$router.addRoutes(this.$store.getters.addRouters);
            if (this.$route.query.to) {
              // noinspection ES6MissingAwait,JSCheckFunctionSignatures
              this.$router.push({ path: this.$route.query.to });
            } else {
              // noinspection ES6MissingAwait
              this.$router.push({ name: '概览' });
            }
          })
        } catch (e) {
          
          
          
          this.loading = false;
          this.refreshCaptcha();
        }
      }
    }
  }
</script>

<style lang="scss">

</style>
