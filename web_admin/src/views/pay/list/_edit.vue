<template>
<el-dialog :title="actLabel" :visible.sync="visible" :close-on-click-modal="false">
  <el-form :model="form" :rules="formRules" label-width="70px" ref="form">

    <el-form-item label="名称" prop="name">
      <el-input type="text" placeholder="请输入名称" v-model="form.name">
      </el-input>
    </el-form-item>

    <el-form-item label="费率" prop="fee_system">
      <el-input type="text" placeholder="支付通道费率" v-model="form.fee_system">
      </el-input>
    </el-form-item>

    

    <el-form-item label="启用">
      <el-checkbox v-model="form.enabled" :true-label="1" :false-label="0">启用</el-checkbox>
    </el-form-item>

    <el-form-item label="驱动" prop="driver">
      <el-input type="text" placeholder="支付驱动代码" v-model="form.driver">
      </el-input>
    </el-form-item>

    <el-form-item label="方式" prop="way">
      <el-input type="text" placeholder="支付方式代码" v-model="form.way">
      </el-input>
    </el-form-item>

    <el-form-item label="备注" prop="comment">
      <el-input type="textarea" :autosize="{ minRows: 2, maxRows: 4}" :maxlength="100"
                placeholder="请输入备注" v-model="form.comment">
      </el-input>
    </el-form-item>

    <div>
      <el-form-item label="配置">
        <json-config ref="json-config" :value="form.config"/>
      </el-form-item>
    </div>

  </el-form>
  <div slot="footer" class="dialog-footer">
    <el-button @click.native="handleClose(false)">取消</el-button>
    <el-button type="primary" @click.native="handleSubmit" :loading="loading">{{actLabel}}</el-button>
  </div>
</el-dialog>
</template>


<script>
import JsonConfig from '~/components/JsonConfig';
import { edit } from '~/api/pay';
export default {
  props: {
    act: {
      type: String,
      default: 'add'
    },
    info: {}
  },
  components: {
    JsonConfig
  },
  data() {
    let actLabel = '';
    const initData = {
      act: this.act,
      id: -1,
      name: '',
      way: '',
      driver: '',
      comment: '',
      config: '',
      fee: '',
      fee_system: '',
      enabled: 0
    };
    if (this.act === 'edit') {
      actLabel = '编辑';
      Object.assign(initData, this.info);
    } else {
      actLabel = '添加';
    }
    return {
      loading: false,
      visible: true,
      actLabel,
      formRules: {
        name: [{
          required: true,
          message: '请输入名称',
          trigger: 'blur'
        }],
        way: [{
          required: true,
          message: '请输入支付方式代码',
          trigger: 'blur'
        }],
        fee_system: [{
          required: true,
          message: '请输入支付通道费率',
          trigger: 'blur'
        }],
        driver: [{
          required: true,
          message: '请输入支付驱动',
          trigger: 'blur'
        }]
      },
      form: initData
    };
  },
  watch: {
    visible(val) {
      if (!val) {
        this.$emit('close', false);
      }
    }
  },
  mounted() {},
  methods: {
    handleSubmit() {
      this.$refs['form'].validate(valid => {
        if (!valid) return;
        this.loading = true;
        this.form.config = this.$refs['json-config'].getText();
        edit(this.form).then(() => {
          this.loading = false;
          this.handleClose(true);
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleClose(success = false) {
      this.visible = false;
      this.$emit('close', success);
    }
  }
};
</script>


<style scoped>
  .el-form-item {
    margin-bottom: 20px !important;
  }
</style>
