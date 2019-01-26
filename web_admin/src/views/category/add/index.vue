<template>
  <div :class="{'app-container': !inBox}">
    <h3 style="margin: 4px 0 28px 0;" v-if="isAdd">添加分类</h3>

    <el-form ref="form" :model="form" :label-width="labelWidth" :rules="formRules">

      <el-form-item label="名称" prop="name">
        <el-input type="text" placeholder="请输入名称" v-model="form.name"/>
      </el-form-item>

      <el-form-item label="排序" prop="sort">
        <el-tooltip effect="dark" content="数字越小越靠前" placement="top-start">
          <el-input-number controls-position="right" :min="0" :max="10000000" v-model="form.sort" size="small"/>
        </el-tooltip>
      </el-form-item>

      <el-form-item label="分类密码" prop="password" class="category-password">
        <el-tooltip effect="dark" content="设置分类密码，开启后只有输入密码才能查看" placement="top-start">
          <el-input type="text" placeholder="分类密码(可选)" v-model="form.password">
            <el-checkbox slot="prepend" v-model="form.password_open">开启</el-checkbox>
          </el-input>
        </el-tooltip>
      </el-form-item>

      <el-form-item label="状态" prop="enabled">
        <el-radio-group v-model="form.enabled" size="small">
          <el-radio-button :label="1">启用</el-radio-button>
          <el-radio-button :label="0" class="el-radio-gray">禁用</el-radio-button>
        </el-radio-group>
      </el-form-item>

      <el-form-item v-if="isAdd">
        <el-button type="primary" @click="handleSubmit" :loading="loading">添加</el-button>
      </el-form-item>

    </el-form>

  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import { edit } from 'api/category'

  export default {
    props: {
      info: {},
      inBox: {
        default: false
      },
      userInfo: {}
    },
    data () {
      return {
        loading: false,
        labelWidth: this.$store.state.app.isMobile ? '70px' : '90px',
        form: {
          id: null,
          name: '',
          sort: 1000,
          password: '',
          password_open: false,
          enabled: null
        },
        formRules: {
          name: [
            { required: true, message: '请输入名称', trigger: 'blur' },
          ],
          enabled: [
            { required: true, message: '请选择状态', trigger: 'blur' },
          ]
        }
      }
    },
    computed: {
      isAdd () {
        return !this.info
      }
    },
    watch: {
      loading (val, old) {
        this.$emit('update:loading', val);
        if (val) {
          if (!old && !NProgress.isStarted()) NProgress.start();
        } else {
          NProgress.done();
        }
      }
    },
    mounted () {
      if (!this.isAdd) {
        this.form.id = this.info.id;
        this.form.name = this.info.name;
        this.form.sort = this.info.sort;
        this.form.password = this.info.password;
        this.form.password_open = this.info.password_open === 1;
        this.form.enabled = this.info.enabled;
      }
      if (this.userInfo) {
        this.form.user_id = this.userInfo.id;
      }
    },
    methods: {
      handleSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;

          edit(this.form).then(() => {
            this.loading = false;
            this.$notify({
              title: '提示',
              message: (this.action === 'edit' ? '编辑分类' : '创建分类') + '成功',
              type: 'success', duration: 3000
            });
            this.$emit('submit', true);
          }).catch(() => {
            this.loading = false;
          });
        });
      }
    }
  }
</script>

<style lang="scss">
  .category-password {
    @media only screen and (max-width: 480px) {
      .el-input-group__prepend,
      .el-input-group__append {
        padding: 0 4px;
      }
      .el-checkbox__label {
        padding-left: 0;
      }
    }
  }
</style>
