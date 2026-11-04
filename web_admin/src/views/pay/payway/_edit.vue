<template>
<div :class="{'app-container': !inBox}">
  <h3 style="margin: 4px 0 28px 0;">{{ isAdd ? '添加' : '编辑' }}</h3>

  <el-form ref="form" :model="form" :label-width="labelWidth" :rules="formRules">

    <el-form-item :label="type === PAYWAY_TYPE.SHOP ? '名称' : 'payway'" prop="name">
      <el-input type="text" placeholder="请输入名称(payway)" v-model="form.name">
      </el-input>
    </el-form-item>

    <el-form-item label="图片" prop="img" :rules="[
          { required: true, message: '请输入支付方式图片', trigger: 'blur' }
        ]" v-if="type === PAYWAY_TYPE.SHOP">
      <el-input type="text" placeholder="图片" v-model="form.img">
      </el-input>
    </el-form-item>

    <el-form-item label="子渠道" prop="channels" class="pay_channels">
      <div>
        <a @click="form.channels.push([0,1])">添加</a>
        <a @click="getPays" style="margin-left: 8px;">刷新</a>
        <div class="channel-body">
          <div v-for="(item,index) in form.channels" :key="index">
            <label class="channel-item">渠道
              <input v-if="pays.length===0" type="number" v-model="item[0]"/>
              <select v-else v-model="item[0]">
                <option v-for="pay in pays" :value="pay.id" :key="pay.id">
                  [{{pay.id}}]&nbsp;{{pay.enabled===0?'[-]&nbsp;':''}}{{pay.name}}&nbsp;&nbsp;({{pay.comment}})
                </option>
              </select>
            </label>
            <label class="channel-item">权重
              <input type="number" max="10000000" v-model="item[1]"/>
              <a style="cursor: pointer;" @click="form.channels.splice(index,1)">删除</a>
            </label>
          </div>
        </div>
      </div>
    </el-form-item>

    

    <el-form-item label="备注" prop="comment">
      <el-input type="textarea" placeholder="备注信息" v-model="form.comment"
                :autosize="{ minRows: 1, maxRows: 10}">
      </el-input>
    </el-form-item>

    <el-form-item label="启用" v-if="type === PAYWAY_TYPE.SHOP">
      <el-checkbox-group v-model="ENABLE_IN">
        <el-checkbox :label="PAYWAY_ENABLED.PC.id">电脑端</el-checkbox>
        <el-checkbox :label="PAYWAY_ENABLED.MOBILE.id">手机端</el-checkbox>
      </el-checkbox-group>
    </el-form-item>

    <el-form-item label="启用" v-else>
      <el-checkbox v-model="form.enabled" :true-label="1" :false-label="0">启用</el-checkbox>
    </el-form-item>

    <el-form-item>
      <el-button type="primary" @click="handleSubmit" :loading="loading">{{ isAdd ? '添加': '编辑' }}</el-button>
    </el-form-item>

  </el-form>

</div>
</template>

<script>
import NProgress from 'nprogress';
import { edit, TYPE as PAYWAY_TYPE, PAYWAY_ENABLED } from '~/api/payway';
import { getList as getPays } from '~/api/pay';
export default {
  props: {
    info: {},
    inBox: {
      default: false
    },
    type: {}
  },
  data() {
    const ENABLE_IN = [];
    if (this.info) {
      if (this.info.enabled & PAYWAY_ENABLED.PC.id) {
        ENABLE_IN.push(PAYWAY_ENABLED.PC.id);
      }
      if (this.info.enabled & PAYWAY_ENABLED.MOBILE.id) {
        ENABLE_IN.push(PAYWAY_ENABLED.MOBILE.id);
      }
    }
    return {
      loading: false,
      isMobile: this.$store.state.app.isMobile,
      labelWidth: this.$store.state.app.isMobile ? '70px' : '90px',
      PAYWAY_TYPE,
      PAYWAY_ENABLED,
      pays: [],
      form: {
        id: 0,
        type: this.type,
        name: '',
        img: '',
        sort: 1000,
        channels: [],
        user_ids: '',
        comment: '',
        enabled: 0
      },
      formRules: {
        name: [{
          required: true,
          message: '请输入名称',
          trigger: 'blur'
        }],
        enabled: [{
          required: true,
          message: '请选择状态',
          trigger: 'blur'
        }]
      },
      ENABLE_IN
    };
  },
  computed: {
    isAdd() {
      return !this.info;
    }
  },
  watch: {
    loading(val, old) {
      this.$emit('update:loading', val);
      if (val) {
        if (!old && !NProgress.isStarted()) NProgress.start();
      } else {
        NProgress.done();
      }
    }
  },
  beforeMount() {
    if (!this.isAdd) {
      this.form = Object.assign({}, this.info);
      this.form.channels = this.form.channels.slice(); // copy

      if (this.form.user_ids && this.form.user_ids.length) {
        const tmp = JSON.stringify(this.form.user_ids);
        this.form.user_ids = tmp.substr(1, tmp.length - 2); // [1,2,3,4] -> 1,2,3,4  数组到文本,并删掉括号[]
      }
    }
    this.getPays();
  },
  methods: {
    getPays() {
      getPays({
        search: 'simple',
        val: 'enabled'
      }).then(res => {
        this.pays = res.data;
      });
    },
    handleSubmit() {
      this.$refs['form'].validate(valid => {
        if (!valid) return;
        this.loading = true;
        const buf = Object.assign({}, this.form);
        buf.channels = JSON.stringify(buf.channels);
        if (buf.user_ids) buf.user_ids = `[${buf.user_ids}]`; // [2,55,66]
        this.ENABLE_IN.forEach(e => buf.enabled |= e);
        edit(buf).then(() => {
          this.loading = false;
          this.$notify({
            title: '提示',
            message: (this.action === 'edit' ? '编辑' : '创建') + '成功',
            type: 'success',
            duration: 3000
          });
          this.$emit('submit', true);
        }).catch(() => {
          this.loading = false;
        });
      });
    }
  }
};
</script>


<style lang="scss" scoped>
  .pay_channels {
    color: #606266;

    .channel-body {
      line-height: initial;

      & > div {
        line-height: 32px;
      }
    }

    a {
      cursor: pointer;
      color: #58B7FF;
      font-size: 14px;
    }

    label {
      white-space: nowrap;
    }

    select {
      max-width: 80%;
    }

    input {
      width: 60px;
    }

    input, select {
      border: 1px solid #dcdfe6;
      outline: 0;
      border-radius: 2px;
      padding: 0 4px;
      transition: border-color .2s cubic-bezier(.645, .045, .355, 1);
      line-height: 20px;
      height: 20px;
      vertical-align: middle;
      margin-right: 4px;
      font-size: 12px;
    }

    @media only screen and (max-width: 600px) {
      .channel-item {
        display: block;
      }
    }

    select:hover, input:hover {
      border-color: #409EFF;
    }
  }
</style>
