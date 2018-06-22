<template>
  <mu-dialog :open="true" :title="act==='add'?'添加':'编辑'" @close="editCallback(false)" scrollable>
    <mu-text-field label="名称" hintText="请输入名称" v-model="form.name" class="edit-input" labelFloat/>
    <mu-text-field label="图片" hintText="请输入图片URL" v-model="form.img" class="edit-input" labelFloat/>
    <mu-text-field label="驱动" hintText="请输入支付驱动" v-model="form.driver" class="edit-input" labelFloat/>
    <mu-text-field label="方式" hintText="请输入支付方式代码" v-model="form.way" class="edit-input" labelFloat/>
    <mu-text-field label="备注" hintText="请输入备注" multiLine :rows="2" :rowsMax="5" labelFloat
                   v-model="form.comment" class="edit-input"/>


    <div style="margin: 12px 0">
      <span style="display: inline-block;line-height: 24px; vertical-align:top;">配置格式:&nbsp;</span>
      <mu-radio name="showConfig" label="JSON" nativeValue="json"
                v-model="showConfigType" style="vertical-align:top;"/>
      <mu-radio name="showConfig" label="Parse" nativeValue="parse"
                v-model="showConfigType" style="vertical-align:top;"/>
    </div>

    <div v-if="showConfigType==='json'">
      <mu-text-field label="配置" hintText="请输入JSON格式配置" multiLine :rows="7" :rowsMax="50"
                     labelFloat v-model="form.config" class="edit-input"/>
    </div>

    <div v-else>
      <mu-text-field v-for="item in configForm" :key="item.name" :label="item.name" v-model="item.value"
                     class="edit-input"/>
    </div>


    <div style="margin: 12px 0">
      <span style="display: inline-block;line-height: 24px; vertical-align:top;">启用:&nbsp;</span>
      <mu-checkbox name="enableIn" label="电脑端" nativeValue="1" v-model="enableIn" style="vertical-align:top;"/>
      <mu-checkbox name="enableIn" label="手机端" nativeValue="2" v-model="enableIn" style="vertical-align:top;"/>
    </div>

    <mu-flat-button slot="actions" @click="editCallback(false)" primary label="取消"/>
    <mu-flat-button slot="actions" primary @click="handleEditSubmit" label="确定"/>
  </mu-dialog>
</template>

<script>
  export default {
    props: {
      act: {},
      info: {}
    },
    data () {
      const initData = {
        name: '',
        img: '',
        driver: '',
        way: '',
        comment: '',
        config: ''
      }
      const enableIn = []
      if (this.act === 'edit') {
        Object.assign(initData, this.info);
        if (this.info.enabled & 1) {
          enableIn.push(1);
        }
        if (this.info.enabled & 2) {
          enableIn.push(2);
        }
      }
      return {
        enableIn,
        form: initData,
        showConfigType: 'json',
        configForm: []
      }
    },
    watch: {
      showConfigType () {
        this.syncConfig();
      }
    },
    methods: {
      editCallback (ret = false) {
        this.$emit('ret', ret)
      },
      handleEditSubmit () {
        if (this.showConfigType === 'parse') {
          this.syncConfig(true);
        }
        this.form.enabled = 0;
        this.enableIn.forEach(e => this.form.enabled |= e);
        this.$http.post('admin/pay/edit', this.form).then(() => {
          this.editCallback(true)
        })
      },
      syncConfig (sync = false) {
        if (sync || this.showConfigType === 'json') {
          const json = {};
          this.configForm.forEach(e => json[e.name] = e.value);
          this.form.config = JSON.stringify(json, null, 2);
        } else {
          try {
            const arr = [];
            const json = eval(`(function(){return ${this.form.config}})()`);
            for (const key in json) {
              if (json.hasOwnProperty(key)) {
                arr.push({ name: key, value: json[key] })
              }
            }
            this.configForm = arr;
          } catch (e) {
            this.showConfigType = 'json';
            this.$alert(e, '配置出错');
          }
        }
      },
    }
  }
</script>

<style scoped>
  .edit-input{
    display: block !important;
    width: 100% !important;
  }
</style>
