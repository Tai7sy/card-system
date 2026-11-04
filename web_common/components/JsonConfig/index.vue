<template>
  <el-form label-position="top">
    <el-form-item>
      <el-radio-group v-model="isJsonText" @change="onModeChange">
        <el-radio :label="true">JSON</el-radio>
        <el-radio :label="false">Parse</el-radio>
      </el-radio-group>
    </el-form-item>

    <div v-if="isJsonText">
      <el-form-item>
        <el-input type="textarea" :autosize="{ minRows: configForm.length ? 7 : 1, maxRows: 20}"
                  placeholder="请输入JSON格式配置" v-model="configText">
        </el-input>
      </el-form-item>
    </div>

    <div v-else>
      <el-form-item v-for="item in configForm" :key="item.name" :label="item.name">
        <el-input type="text" v-model="item.value">
        </el-input>
      </el-form-item>
    </div>
  </el-form>
</template>

<script>
  export default {
    name: "JsonConfig",
    props: {
      value: {
        type: String
      },
      showJson: {
        type: Boolean
      }
    },
    data() {
      return {
        isJsonText: this.showJson,
        configText: '',
        configForm: []
      }
    },
    beforeMount() {
      this.setText(this.value || '{}');
      if (this.configForm.length === 0) {
        this.isJsonText = true;
      }
    },
    methods: {
      onModeChange(isJsonText) {
        if (isJsonText) {
          const json = {};
          this.configForm.forEach(e => json[e.name] = e.value);
          this.configText = JSON.stringify(json, null, 2);
        } else {
          try {
            const arr = [];
            const json = eval(`(function(){return ${this.configText}})()`);
            for (const key in json) {
              if (Object.prototype.hasOwnProperty.call(json, key)) {
                arr.push({name: key, value: json[key]})
              }
            }
            this.configForm = arr;
          } catch (e) {
            this.$alert(`json配置格式异常: <br>${e.message}`, '错误', {dangerouslyUseHTMLString: true, type: 'error'});
            this.isJsonText = true;
          }
        }
      },
      /**
       * 设置文本格式的内容
       */
      setText(val) {
        this.configText = val;
        this.onModeChange();
      },
      /**
       *  获取文本格式的内容
       */
      getText() {
        this.onModeChange(!this.isJsonText);
        return this.configText;
      }
    }
  }
</script>

<style scoped>

</style>