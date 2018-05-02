<template>
  <mu-flexbox justify="center" align="center" class="login-panel">
    <mu-flexbox-item>
      <mu-dialog :open="!!process" title="提示">
        <mu-circular-progress :size="40" style="vertical-align:middle;"/>
        <span v-html="process"
              style="line-height: 40px;display:inline-block;margin-left: 24px"></span>
      </mu-dialog>

      <div style="margin-top: 40%">
        <mu-text-field hintText="请输入账号" v-model="username" type="text" class="my-text-field"/>
        <mu-text-field hintText="请输入密码" v-model="password" type="password" class="my-text-field"/>
        <mu-checkbox label="记住密码" class="demo-checkbox" v-model="remember"/>
        <br/>
        <mu-raised-button label="登录" class="my-raised-button" primary @click="passLogin"/>
      </div>


    </mu-flexbox-item>
  </mu-flexbox>
</template>


<script>
  import myFunc from '../../../utils/myfunc.js'

  export default {
    data () {
      return {
        process: false,
        username: '',
        password: '',
        remember: false,
      }
    },
    mounted () {
      this.username = myFunc.cookie.get('l_u') || '';
      this.password = myFunc.cookie.get('l_p') || '';
      if (this.username.length) {
        this.remember = true;
      }
    },
    methods: {
      passLogin () {
        if (this.username.length < 1) {
          this.$alert('账号输入错误');
          return
        }
        if (this.password.length < 1) {
          this.$alert('请输入密码');
          return
        }
        /**
         * 记住密码  将密码保存到cookie
         */
        myFunc.cookie.set('l_u', this.remember ? this.username : null);
        myFunc.cookie.set('l_p', this.remember ? this.password : null);

        this.process = '登录中, 请稍后';

        this.$http.post('admin/login', {
          username: this.username,
          password: this.password,
          remember: this.remember,
          _t: Math.random(),
        }).then(ret => {
          this.process = false;
          if (ret.code === undefined) {
            this.$alert('由于网络出现了异常，登录失败');
            return;
          }
          if (ret.code === 0) {
            myFunc.cookie.set('login', '1')
            this.$router.push('dashboard');
          } else {
            this.$alert(ret.msg);
          }
        }).catch(() => {
          this.process = false;
        });
      },
    }
  }
</script>

<style lang="less" type="text/less" scoped>
  .login-panel {
    padding: 24px;
    max-width: 320px;
    margin: auto;
    height: 70%;
  }

  @media (min-width: 375px) {
    .login-panel {
      max-width: 360px;
    }
  }

  @media (min-width: 410px) {
    .login-panel {
      max-width: 395px;
    }
  }

  //大屏幕后缩小到原来
  @media (min-width: 993px) {
    .login-panel {
      max-width: 350px;
    }
  }

  .my-text-field {
    width: 100%;
  }

  .my-raised-button {
    border-radius: 4px;
    width: 100%;
    margin: 12px auto 16px auto;
  }

  a {
    color: #03a9f4;
  }

  .capture {
    width: 130px;
    height: 53px;
  }
</style>
