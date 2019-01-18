<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">常规设置</h2>

      <el-form-item prop="app_name" class="item-container">
        <span>名称</span>
        <el-input type="text" placeholder="网站名称" v-model="form.app_name"/>
      </el-form-item>

      <el-form-item prop="app_title" class="item-container">
        <span>标题</span>
        <el-input type="text" placeholder="网站标题" v-model="form.app_title"/>
      </el-form-item>

      <el-form-item prop="shop_bkg" class="item-container">
        <span>店铺背景</span>
        <el-input type="text" placeholder="店铺背景(图片URL)" v-model="form.shop_bkg"/>
      </el-form-item>

      <!--el-form-item label="店铺主题" prop="shop_template_id">
        <el-select v-model="form.shop_template_id">
          <el-option label="默认主题" :value="0"/>
          <el-option v-for="item in templates"
                     :key="item.id" :label="item.name" :value="item.id"/>
        </el-select>
      </el-form-item-->

      <el-form-item prop="shop_ann" class="item-container">
        <span>店铺公告</span>
        <quill-editor ref="editor-shop_ann" placeholder="本说明将显示在商户首页"/>
      </el-form-item>


      <el-form-item prop="shop_ann_pop" class="item-container">
        <span>首页弹窗</span>
        <quill-editor ref="editor-shop_ann_pop" placeholder="本说明将在商户首页弹窗"/>
      </el-form-item>


      <el-form-item class="item-container">
        <span>库存显示</span>
        <el-radio-group v-model="form.shop_inventory" size="small">
          <el-radio-button :label="0">大致范围</el-radio-button>
          <el-radio-button :label="1">实际库存</el-radio-button>
        </el-radio-group>
      </el-form-item>


      <h2 class="title" style="margin-top: 48px">网站设置</h2>
      <el-form-item prop="app_url" class="item-container">
        <span>网站地址</span>
        <el-input type="text" placeholder="格式: http://example.com" v-model="form.app_url"/>
      </el-form-item>
      <el-form-item prop="app_url_api" class="item-container">
        <span>网站API地址</span>
        <el-input type="text" placeholder="格式: http://api.example.com" v-model="form.app_url_api"/>
        <span style="font-size: 12px">用于支付API通知，如主站开启5秒盾等，请在这里提供一个没有盾的地址，否则会造成通知失败</span>
      </el-form-item>
      <el-form-item prop="keywords" class="item-container">
        <span>关键词</span>
        <el-input type="text" placeholder="请输入网站关键词(keywords)" v-model="form.keywords"/>
      </el-form-item>
      <el-form-item prop="description" class="item-container">
        <span>描述</span>
        <el-input type="text" placeholder="请输入网站描述(description)" v-model="form.description"/>
      </el-form-item>

      <el-form-item prop="qq" class="item-container">
        <span>客服QQ</span>
        <el-input type="text" placeholder="请输入联系QQ" v-model="form.shop_qq"/>
      </el-form-item>

      <el-form-item prop="js_kf" class="item-container">
        <span>客服代码(QQ等)</span>
        <el-input type="textarea" placeholder="请输入客服代码" v-model="form.js_kf"
                  :autosize="{ minRows: 3, maxRows: 15}"/>
      </el-form-item>

      <el-form-item prop="js_tj" class="item-container">
        <span>统计代码</span>
        <el-input type="textarea" placeholder="请输入统计代码" v-model="form.js_tj"
                  :autosize="{ minRows: 3, maxRows: 15}"/>
      </el-form-item>

    </el-form>
    <div class="text-center" style="margin-top: 24px">
      <el-button @click="handleResetForm">刷新</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">保存更改</el-button>
    </div>
  </el-card>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import { info } from '@/api/system'
  import QuillEditor from '@/components/QuillEditor/index';

  export default {
    components: { QuillEditor },
    data () {
      return {
        loading: false,
        labelWidth: '0px', // this.$store.state.app.isMobile ? '70px' : '90px',
        form: {
          app_name: '加载中',
          app_title: '',
          app_url: '',
          app_url_api: '',
          keywords: '',
          description: '',
          shop_bkg: '',
          shop_ann: '',
          shop_inventory: 0,
          shop_qq: '',
          js_tj: '',
          js_kf: ''
        },
        formRules: {
          app_name: [
            { required: true, message: '请输入网站名称', trigger: 'blur' }
          ],
          app_url: [
            { required: true, message: '请输入网站地址', trigger: 'blur' }
          ],
          app_url_api: [
            { required: true, message: '请输入网站API地址', trigger: 'blur' }
          ],
          keywords: [
            { required: true, message: '请输入网站关键词', trigger: 'blur' }
          ]
        }
      }
    },
    mounted () {
      this.loadInfo();
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
      loadInfo () {
        this.loading = true;
        info().then(res => {
          Object.assign(this.form, res.data);
          this.$refs['editor-shop_ann'].setValue(this.form.shop_ann);
          this.$refs['editor-shop_ann_pop'].setValue(this.form.shop_ann_pop);
          this.loading = false;
        })
      },
      handleResetForm () {
        this.loadInfo();
      },
      handleSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;
          this.form.shop_ann = this.$refs['editor-shop_ann'].getValue();
          this.form.shop_ann_pop = this.$refs['editor-shop_ann_pop'].getValue();
          info(this.form).then(() => {
            this.$notify({ title: '提示', message: '修改设置成功', type: 'success', duration: 3000 });
            this.loading = false;
          }).catch(() => {
            this.loading = false;
          });
        });
      }
    }
  }
</script>
