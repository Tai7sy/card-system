<template>
  <div class="app-container">
    <h3 style="margin: 4px 0 28px 0;">修改密码</h3>
    <el-form ref="form" :model="form" label-width="80px" :rules="formRules">


      <el-form-item label="旧密码" prop="old">
        <el-input type="password" placeholder="请输入旧密码" v-model="form.old">
        </el-input>
      </el-form-item>

      <el-form-item label="新密码" prop="password">
        <el-input type="password" placeholder="请输入新密码" v-model="form.password">
        </el-input>
      </el-form-item>

      <el-form-item label="新密码" prop="password_confirmation">
        <el-input type="password" placeholder="请再次输入新密码" v-model="form.password_confirmation">
        </el-input>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="onSubmit" :loading="loading">确认修改</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import NProgress from 'nprogress'
  import { changePass } from '@/api/auth'

  export default {
    data () {
      return {
        loading: false,
        form: {
          old: '',
          password: '',
          password_confirmation: ''
        },
        formRules: {
          old: [
            { required: true, message: '请输入旧密码', trigger: 'blur' }
          ],
          password: [
            {
              required: true, validator: (rule, value, callback) => {
                if (value === '') {
                  callback(new Error('请输入密码'));
                } else {
                  if (this.form.password_confirmation !== '') {
                    this.$refs.form.validateField('password_confirmation');
                  }
                  callback();
                }
              }, trigger: 'blur'
            },
            { min: 6, max: 16, message: '长度在 6 到 16 个字符', trigger: 'blur' }
          ],
          password_confirmation: [
            {
              required: true, validator: (rule, value, callback) => {
                if (value === '') {
                  callback(new Error('请再次输入密码'));
                } else if (value !== this.form.password) {
                  callback(new Error('两次输入密码不一致!'));
                } else {
                  callback();
                }
              }, trigger: 'blur'
            },
            { min: 6, max: 16, message: '长度在 6 到 16 个字符', trigger: 'blur' }
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
    methods: {
      onSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;
          const bufInfo = Object.assign({}, this.form);
          changePass(bufInfo).then(() => {
            this.loading = false;
            this.$notify({ title: '提示', message: '修改密码成功', type: 'success', duration: 3000 });
          }).catch(() => {
            this.loading = false;
          });
        });
      }
    }
  }
</script>
