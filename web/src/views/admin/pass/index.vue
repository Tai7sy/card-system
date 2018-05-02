<template>
  <div class="content-wrapper">
    <mu-row gutter>
      <mu-col width="100" tablet="100" desktop="100" style="padding-top: 0">
        <mu-paper class="content-wrapper" :zDepth="1">
          <mu-text-field label="新密码" hintText="请输入新密码" v-model="password"/>
          <br/>
          <mu-raised-button label="修改密码" @click="handleChange"/>
        </mu-paper>
      </mu-col>
    </mu-row>
    <mu-dialog :open="!!process" title="提示">
      <mu-circular-progress :size="40" style="vertical-align:middle;"/>
      <span v-html="process"
            style="line-height: 40px;display:inline-block;margin-left: 24px"></span>
    </mu-dialog>
  </div>
</template>

<script>
  import myFunc from '../../../utils/myfunc.js'

  export default {
    data () {
      return {
        password: '',
        process: null
      }
    },
    mounted () {
      this.checkLogin();
    },
    methods: {
      checkLogin () {
        if (!myFunc.cookie.get('login')) {
          this.$router.push('/admin/login');
        }
      },
      handleChange () {
        if (!this.password) {
          return this.$alert('请输入新密码');
        }
        this.process = '修改中';
        this.$http.post('admin/auth/change', {
          password: this.password
        }).then(() => {
          this.$alert('修改成功');
          this.process = null;
        }).catch(() => {
          this.process = null;
        })
      }
    }
  }
</script>
