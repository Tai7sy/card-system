<template>
  <div class="app-container">

    <el-card class="my-card">
      <div class="about-card-body">
        <h2 class="title">系统维护</h2>
        <el-button @click="clearCache" size="small">清空缓存</el-button>
        <el-button size="small" v-loading="!token">
          <a :href="'/api/admin/web/logs/' + token" target="_blank">系统日志</a>
        </el-button>
      </div>
    </el-card>

    <el-card class="my-card">
      <div v-if="can_update" class="about-card-body">
        <h2 class="title">检查更新</h2>
        <p class="update-tip" v-html="updateTip"></p>
      </div>
      <div v-else>
        您的版本不支持更新
      </div>
    </el-card>
  </div>
</template>

<script>
  export default {
    components: {},
    data() {
      return {
        can_update: true,
        version_checking: 0,
        version: 'unknown',
        version_latest: '',
        version_latest_description: '',
        token: null
      }
    },
    computed: {
      updateTip() {
        if (this.version_checking) {
          return '正在检查更新...';
        }
        if (!this.version_latest)
          return '检查更新失败';
        if (this.version_latest !== this.version) {
          return `当前版本: ${this.version}<br>
最新版本: <b>${this.version_latest}</b><br><br>

请先备份数据库与配置，再按仓库 README 中的源码更新流程操作。<br>
<code>php artisan update</code> 可查看更新步骤。` + (this.version_latest_description ? ('<br><br>' + this.version_latest_description.replace(/\n/g, '<br>')) : '')
        }
        return `当前版本 ${this.version} 已是最新版<br>`;
      }
    },
    mounted() {
      this.token = encodeURIComponent(this.$store.state.user.token);

      this.$http.get('web/logs/token').then(res => {
        this.token = res.token;
      });

      if (this.can_update) {
        this.checkUpdate();
      }
    },
    methods: {
      clearCache() {
        this.$http.post('web/cache/clear').then(() => {
          this.$notify({
            title: '成功',
            message: '系统缓存已经成功清除，若仍有问题请检查CDN缓存',
            type: 'success'
          })
        });
      },
      checkUpdate() {
        this.version_checking = 1;
        this.$http.get('version').then(res => {
          this.version = res.data.version;
          this.version_latest = res.data.update.version;
          this.version_latest_description = res.data.update.description;
        }).finally(() => {
          this.version_checking--;
        });
      }
    }
  }
</script>
<style lang="scss" scoped>
  .title {
    font-weight: normal;
    margin: 0 0 12px 0;
  }
</style>

<style lang="scss">
  .about-card-body {
    padding: 4px 8px;
  }

  .update-tip {
    font-size: 14px;

    code {
      background-color: #eee;
      display: inline-block;
      padding: 2px 4px;
      border-radius: 4px;
    }
  }
</style>
