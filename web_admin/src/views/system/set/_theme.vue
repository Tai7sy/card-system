<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">主题设置</h2>

      <el-form-item prop="shop_theme" class="item-container">
        <span style="display: block">默认主题</span>
        <el-select class="theme-select" v-model="theme" @change="form.shop_theme = theme.name">
          <el-option v-for="item in themes"
                     :key="'theme_'+item.id"
                     :label="item.name + ' - ' + item.description"
                     :value="item"/>
        </el-select>
      </el-form-item>

      <el-card class="child-option" v-if="theme">
        <p class="child-option-title">主题默认配置</p>
        <el-form-item class="item-container" v-for="(option, name) in theme.options" :key="'theme_option_'+name">
          <span v-if="option.type!=='checkbox'"
                style="display: block"
          >{{option.label}}</span>

          <el-input v-if="option.type==='text'"
                    v-model="option.value"
                    :type="option.inputType || 'text'"
                    :maxlength="option.size"
                    :placeholder="option.placeholder || ''"/>

          <el-input v-if="option.type==='textarea'"
                    v-model="option.value"
                    type="textarea"
                    :maxlength="option.size"
                    :rows="option.rows"
                    :placeholder="option.placeholder || ''"/>


          <el-checkbox v-if="option.type==='checkbox'"
                       v-model="option.value"
                       :true-label="1" :false-label="0">
            {{option.label}}
          </el-checkbox>

          <el-select v-if="option.type==='select'"
                     v-model="option.value">
            <el-option v-for="(option2,name2) in option.values"
                       :key="'theme_option_' + name + '_' + name2"
                       :value="name2"
                       :label="option2.label"/>
          </el-select>

          <el-radio-group v-if="option.type==='radio'"
                          v-model="option.value">
            <el-radio v-for="(option2,name2) in option.values"
                      :key="'theme_option_' + name + '_' + name2"
                      :label="name2">{{option2.label}}
            </el-radio>
          </el-radio-group>
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
  import { theme } from '@/api/system';

  export default {
    components: {},
    data () {
      return {
        loading: false,
        labelWidth: '0px', 
        themes: [],
        theme: null,
        form: {
          shop_theme: '',
        },
        formRules: {},
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
          this.themes.forEach(theme => {
            theme.value = theme.name;
            if (theme.options && theme.config) {
              for (const key in theme.options) {
                if (Object.prototype.hasOwnProperty.call(theme.options, key) && Object.prototype.hasOwnProperty.call(theme.config, key)) {
                  theme.options[key].value = theme.config[key]
                }
              }
            }
          });
          this.form.shop_theme = res.data.default;
          this.theme = this.themes.find(e => e.name === this.form.shop_theme);
        }).finally(() => {
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
          this.form.theme_config = {};
          Object.keys(this.theme.options).map(key => {
            this.form.theme_config[key] = this.theme.options[key].value;
          });
          this.form.theme_config = JSON.stringify(this.form.theme_config);
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

  .theme-select {
    display: block;
  }

  .child-option {
    box-shadow: none !important;
    margin-bottom: 16px;

    .child-option-title {
      margin: 0 0 4px 0;
    }
  }
</style>


