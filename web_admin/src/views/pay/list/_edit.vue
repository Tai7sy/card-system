<template>
  <el-dialog :title="actLabel" :visible.sync="visible">
    <el-form :model="form" :rules="formRules" label-width="70px" ref="form">

      <el-form-item label="名称" prop="name">
        <el-input type="text" placeholder="请输入名称" v-model="form.name">
        </el-input>
      </el-form-item>
      <el-form-item label="图片" prop="img">
        <el-input type="text" placeholder="图片" v-model="form.img">
        </el-input>
      </el-form-item>

      <el-form-item label="方式" prop="way">
        <el-input type="text" placeholder="支付方式代码" v-model="form.way">
        </el-input>
      </el-form-item>

      <el-form-item label="费率" prop="fee_system">
        <el-input type="text" placeholder="支付通道费率" v-model="form.fee_system">
        </el-input>
      </el-form-item>

      <el-form-item label="启用">
        <el-checkbox-group v-model="enableIn">
          <el-checkbox :label="paywayEnabled.PC.id">电脑端</el-checkbox>
          <el-checkbox :label="paywayEnabled.MOBILE.id">手机端</el-checkbox>
        </el-checkbox-group>
      </el-form-item>

      <el-form-item label="驱动" prop="driver">
        <el-input type="text" placeholder="支付驱动代码" v-model="form.driver">
        </el-input>
      </el-form-item>
      <el-form-item label="备注" prop="comment">
        <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" :maxlength="100"
                  placeholder="请输入备注" v-model="form.comment">
        </el-input>
      </el-form-item>

      <div>
        <el-form-item label="配置">
          <el-radio-group v-model="showJson" @change="syncConfig">
            <el-radio :label="true">JSON</el-radio>
            <el-radio :label="false">Parse</el-radio>
          </el-radio-group>
        </el-form-item>

        <div v-if="showJson">
          <el-form-item>
            <el-input type="textarea" :autosize="{ minRows: 7, maxRows: 20}"
                      placeholder="请输入JSON格式配置" v-model="form.config">
            </el-input>
          </el-form-item>
        </div>

        <div v-else>
          <el-form-item v-for="item in this.configForm" :key="item.name" :label="item.name">
            <el-input type="text" v-model="item.value">
            </el-input>
          </el-form-item>
        </div>
      </div>

    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">{{actLabel}}</el-button>
    </div>
  </el-dialog>
</template>


<script>
  import { paywayEnabled, edit } from 'api/pay'

  export default {
    props: {
      act: {
        type: String,
        default: 'add'
      },
      info: {}
    },
    data () {
      let actLabel = '';
      const initData = {
        act: this.act,
        id: -1,
        name: '',
        way: '',
        driver: '',
        comment: '',
        config: '',
        img: '',
        fee_system: ''
      };
      const enableIn = [];
      if (this.act === 'edit') {
        actLabel = '编辑';
        Object.assign(initData, this.info);
        if (this.info.enabled & paywayEnabled.PC.id) {
          enableIn.push(paywayEnabled.PC.id);
        }
        if (this.info.enabled & paywayEnabled.MOBILE.id) {
          enableIn.push(paywayEnabled.MOBILE.id);
        }
      } else {
        actLabel = '添加';
      }
      return {
        loading: false,
        visible: true,
        actLabel,
        paywayEnabled,
        formRules: {
          name: [
            { required: true, message: '请输入名称', trigger: 'blur' }
          ],
          fee_system: [
            { required: true, message: '请输入支付通道费率', trigger: 'blur' }
          ],
        },
        form: initData,
        enableIn,
        showJson: false,
        configForm: []
      }
    },
    watch: {
      visible (val) {
        if (!val) {
          this.$emit('close', false);
        }
      }
    },
    mounted () {
      if (this.act === 'add') {
        this.showJson = true;
      } else if (this.act === 'edit') {
        this.showJson = false;
        this.syncConfig(this.showJson);
      } else {
        this.$alert('error act');
        this.handleClose(false);
      }
      if (this.configForm.length === 0) {
        this.showJson = true;
      }
    },
    methods: {
      syncConfig (showJson) {
        if (showJson) {
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
            this.$alert(e, '配置出错');
            this.showJson = true;
          }
        }
      },
      handleSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;
          if (this.showJson === false) {
            // 如果当前没有显示json, 那么就同步一下 configForm -> json
            this.syncConfig(true);
          }

          this.form.enabled = 0;
          this.enableIn.forEach(e => this.form.enabled |= e);
          edit(this.form).then(() => {
            this.loading = false;
            this.handleClose(true);
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleClose (success = false) {
        this.visible = false;
        this.$emit('close', success);
      }
    }
  }
</script>


<style scoped>
  .el-form-item {
    margin-bottom: 20px !important;
  }
</style>
