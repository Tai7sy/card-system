<template>
  <div class="app-container">
    这里是一些系统功能
    <div style="margin-top: 24px">
      <el-button @click="clearCache" size="small">清空缓存</el-button>
    </div>

    <div style="margin-top: 24px">
      <el-button size="small"><a href="/api/admin/web/logs" target="_blank">系统日志</a></el-button>
    </div>
  </div>
</template>

<script>
  export default {
    components: {},
    data () {
      return {}
    },
    mounted () {
      console.log('\n %c Mail %c 12840640+Tai7sy@users.noreply.github.com \n\n',
        'color: #fadfa3; background: #030307; padding:5px 0;',
        'background: #fadfa3; padding:5px 0;');
    },
    methods: {
      clearCache () {
        this.$http.post('web/cache/clear').then(() => {
          this.$notify({
            title: '成功',
            message: '系统缓存已经成功清除，若仍有问题请检查CDN缓存',
            type: 'success'
          })
        });
      }
    }
  }
</script>
