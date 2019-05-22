<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">存储驱动</h2>
      <el-alert
        title="请根据实际情况配置存储引擎，合理做好站点下载分流。建议尽量使用云存储服务，同时保证文件访问协议与网站访问协议一致！"
        type="warning">
      </el-alert>
      <el-form-item class="item-container">
        <span>驱动</span>
        <el-radio-group v-model="form.storage_driver">
          <el-radio label="local">本地</el-radio>
          <el-radio label="s3">AWS S3</el-radio>
          <el-radio label="oss">阿里云OSS</el-radio>
          <el-radio label="qiniu">七牛云储存</el-radio>
        </el-radio-group>
      </el-form-item>
      <div v-if="form.storage_driver === 'local'" class="item-container">
        <span class="tip">请确保<code>/storage</code>目录已经配置写入权限</span>
      </div>

      <div v-if="form.storage_driver === 's3'">
        <el-form-item prop="storage_s3_access_key" class="item-container">
          <span>AccessKey (访问密钥)</span>
          <el-input type="text" placeholder="请输入 AccessKey (访问密钥)" v-model="form.storage_s3_access_key"/>
        </el-form-item>
        <el-form-item prop="storage_s3_secret_key" class="item-container">
          <span>SecretKey (安全密钥)</span>
          <el-input type="text" placeholder="请输入 SecretKey (安全密钥)" v-model="form.storage_s3_secret_key"/>
        </el-form-item>
        <el-form-item prop="storage_s3_region" class="item-container">
          <span>Region (存储区域)</span>
          <el-input type="text" placeholder="请输入 Region (存储区域)" v-model="form.storage_s3_region"/>
          <span class="tip">填写存储区域代码</span>
        </el-form-item>
        <el-form-item prop="storage_s3_bucket" class="item-container">
          <span>Bucket (空间名称)</span>
          <el-input type="text" placeholder="请输入 Bucket (空间名称)" v-model="form.storage_s3_bucket"/>
          <span class="tip">填写存储空间名称，如：static</span>
        </el-form-item>
      </div>

      <div v-if="form.storage_driver === 'oss'">
        <el-form-item prop="storage_oss_access_key" class="item-container">
          <span>AccessKey (访问密钥)</span>
          <el-input type="text" placeholder="请输入 AccessKey (访问密钥)" v-model="form.storage_oss_access_key"/>
          <span class="tip">可以在 [ 阿里云 > 个人中心 ] 设置并获取到访问密钥</span>
        </el-form-item>
        <el-form-item prop="storage_oss_secret_key" class="item-container">
          <span>SecretKey (安全密钥)</span>
          <el-input type="text" placeholder="请输入 SecretKey (安全密钥)" v-model="form.storage_oss_secret_key"/>
          <span class="tip">可以在 [ 阿里云 > 个人中心 ] 设置并获取到安全密钥</span>
        </el-form-item>
        <el-form-item prop="storage_oss_bucket" class="item-container">
          <span>Bucket (空间名称)</span>
          <el-input type="text" placeholder="请输入 Bucket (空间名称)" v-model="form.storage_oss_bucket"/>
          <span class="tip">填写存储空间名称，如：xxx</span>
        </el-form-item>
        <el-form-item prop="storage_oss_endpoint" class="item-container">
          <span>Endpoint (节点域名)</span>
          <el-input type="text" placeholder="请输入 Endpoint (节点域名)" v-model="form.storage_oss_endpoint"/>
          <span class="tip">例如: oss-cn-hangzhou.aliyuncs.com</span>
        </el-form-item>

        <el-form-item class="item-container">
          <el-switch
            v-model="form.storage_oss_is_cname"
            inactive-text="自定义域名"
            :active-value="1"
            :inactive-value="0">
          </el-switch>
          <br>
          <span class="tip">开启此选项后, 请确认Endpoint为OSS绑定的CNAME域名</span> <br>
          <span class="tip">开启此选项后, 可以使用CDN配置</span>
        </el-form-item>


        <el-form-item class="item-container" v-if="form.storage_oss_is_cname">
          <el-switch
            v-model="form.storage_oss_is_ssl"
            inactive-text="开启SSL"
            :active-value="1"
            :inactive-value="0">
          </el-switch>
        </el-form-item>

        
        <el-form-item prop="storage_oss_cdn_domain" class="item-container" v-if="form.storage_oss_is_cname">
          <span>CDN Domain (CDN 域名)</span>
          <el-input type="text" placeholder="请输入 CDN Domain (CDN 域名)" v-model="form.storage_oss_cdn_domain"/>
          <span class="tip">OSS绑定的CDN域名, 填写后将使用CDN</span>
        </el-form-item>

      </div>

      <div v-if="form.storage_driver === 'qiniu'">
        <el-form-item prop="storage_qiniu_access_key" class="item-container">
          <span>AccessKey (访问密钥)</span>
          <el-input type="text" placeholder="请输入 AccessKey (访问密钥)" v-model="form.storage_qiniu_access_key"/>
          <span class="tip">可以在 [ 七牛云 > 个人中心 ] 设置并获取到访问密钥</span>
        </el-form-item>
        <el-form-item prop="storage_qiniu_secret_key" class="item-container">
          <span>SecretKey (安全密钥)</span>
          <el-input type="text" placeholder="请输入 SecretKey (安全密钥)" v-model="form.storage_qiniu_secret_key"/>
          <span class="tip">可以在 [ 七牛云 > 个人中心 ] 设置并获取到安全密钥</span>
        </el-form-item>
        <el-form-item prop="storage_qiniu_bucket" class="item-container">
          <span>Bucket (空间名称)</span>
          <el-input type="text" placeholder="请输入 Bucket (空间名称)" v-model="form.storage_qiniu_bucket"/>
          <span class="tip">填写存储空间名称，如：static</span>
        </el-form-item>
        <el-form-item prop="storage_qiniu_domains_default" class="item-container">
          <span>Domain (七牛域名)</span>
          <el-input type="text" placeholder="请输入 Domain (七牛域名)" v-model="form.storage_qiniu_domains_default"/>
          <span class="tip">例如: xxxxx.com1.z0.glb.clouddn.com</span>
        </el-form-item>
        <el-form-item prop="storage_qiniu_domains_https" class="item-container">
          <span>HTTPS Domain (HTTPS域名)</span>
          <el-input type="text" placeholder="请输入 HTTPS Domain (HTTPS域名)" v-model="form.storage_qiniu_domains_https"/>
        </el-form-item>

      </div>

    </el-form>
    <div class="text-center" style="margin-top: 24px">
      <el-button @click="handleResetForm">刷新</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">保存更改</el-button>
    </div>
  </el-card>
</template>

<script>
  import NProgress from 'nprogress';
  import { storage } from '~admin/api/system';

  export default {
    components: {},
    data () {
      return {
        loading: false,
        labelWidth: '0px', // this.$store.state.app.isMobile ? '70px' : '90px',
        form: {
          storage_driver: '',
          storage_s3_access_key: '',
          storage_s3_secret_key: '',
          storage_s3_region: '',
          storage_s3_bucket: '',
          storage_oss_access_key: '',
          storage_oss_secret_key: '',
          storage_oss_bucket: '',
          storage_oss_endpoint: '',
          storage_oss_is_ssl: '',
          storage_oss_is_cname: '',
          storage_oss_cdn_domain: '',
          storage_qiniu_access_key: '',
          storage_qiniu_secret_key: '',
          storage_qiniu_bucket: '',
          storage_qiniu_domains_default: '',
          storage_qiniu_domains_https: '',
        },
        formRules: {}
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
        storage().then(res => {
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
          storage(this.form).then(() => {
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


