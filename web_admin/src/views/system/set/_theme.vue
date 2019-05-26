<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">主题设置</h2>

      <el-form-item prop="shop_theme" class="item-container">
        <span style="display: block">默认主题</span>
        <el-select v-model="form.shop_theme">
          <el-option v-for="item in themes" :key="item.id" :label="item.name + ' - ' + item.description"
                     :value="item.name"/>
        </el-select>
      </el-form-item>

      <el-card class="child-option" v-show="form.shop_theme==='Material'">
        <p class="child-option-title">默认主题设置</p>
        <el-form-item class="item-container">
          <span style="display: block">商品排列方式</span>
          <el-select v-model="form.shop_theme_material_list_type">
            <el-option value="dropdown" label="下拉式"/>
            <el-option value="button" label="按钮式"/>
            <el-option value="list" label="列表式"/>
          </el-select>
        </el-form-item>
        <el-form-item class="item-container">
          <span style="display: block">店铺背景</span>
          <el-input type="text" placeholder="店铺背景(图片URL)"
                    v-model="form.shop_theme_material_background" :disabled="themeMaterialNoBackground"/>
          <el-checkbox size="small" v-model="themeMaterialNoBackground">
            不显示背景图片
          </el-checkbox>
        </el-form-item>
      </el-card>
      <el-card class="child-option" v-show="form.shop_theme==='Classic'">
        <p class="child-option-title">默认主题设置</p>
        <el-form-item class="item-container">
          <span style="display: block">商品排列方式</span>
          <el-select v-model="form.shop_theme_classic_list_type">
            <el-option value="dropdown" label="下拉式"/>
            <el-option value="button" label="按钮式"/>
          </el-select>
        </el-form-item>
      </el-card>

    </el-form>
    <div class="text-center" style="margin-top: 24px">
      <el-button @click="handleResetForm">刷新</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">保存更改</el-button>
    </div>
  </el-card>
</template>

<script>
  import NProgress from 'nprogress';
  import { theme } from '~admin/api/system';

  export default {
    components: {},
    data () {
      return {
        loading: false,
        labelWidth: '0px', // this.$store.state.app.isMobile ? '70px' : '90px',
        themes: [],
        form: {
          shop_theme: '',
          shop_theme_material_list_type: '',
          shop_theme_material_background: '',
          shop_theme_classic_list_type: '',
        },
        formRules: {},
        themeMaterialNoBackground: false,
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
        theme().then(res => {
          this.themes = res.data.themes;
          this.themes.forEach(e => {
            if (e.name === 'Material') {
              this.form.shop_theme_material_list_type = e.config.list_type;
              this.form.shop_theme_material_background = e.config.background;
              this.themeMaterialNoBackground = this.form.shop_theme_material_background === '0';
            } else if (e.name === 'Classic') {
              this.form.shop_theme_classic_list_type = e.config.list_type;
            }
          });
          this.form.shop_theme = res.data.default;
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
          if (this.themeMaterialNoBackground) {
            this.form.shop_theme_material_background = '0';
          }
          theme(this.form).then(() => {
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

<style lang="scss" scoped>
  .child-option {
    box-shadow: none !important;
    margin-bottom: 16px;
    .child-option-title {
      margin: 0 0 4px 0;
    }
  }
</style>


