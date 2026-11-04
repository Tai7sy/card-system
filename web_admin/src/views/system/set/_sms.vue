<template>
  <el-card>
    <el-form ref="form" v-loading.body="loading" :model="form" :label-width="labelWidth" :rules="formRules">
      <h2 class="title">短信通知</h2>

      <el-form-item prop="sms_api_id" class="item-container">
        <span>API_ID</span>
        <el-button v-if="!form.sms_api_id && !loading" @click="handleApplySmsAccount" :disabled="loading"
                   class="el-form-item-title-btn" type="text">点击申请
        </el-button>
        <el-input type="text" placeholder="请输入 API_ID" v-model="form.sms_api_id"/>
      </el-form-item>

      <el-form-item prop="sms_api_key" class="item-container">
        <span>API_KEY</span>
        <el-input type="text" placeholder="请输入 API_KEY" v-model="form.sms_api_key"/>
      </el-form-item>


      <el-form-item class="item-container" v-if="form.sms_api_id && form.sms_api_key">
        <span>短信余额</span>
        <el-button type="text" class="el-form-item-title-btn" @click="handleLoadSmsInfo">刷新</el-button>
        <el-button v-if="sms.buyLink" type="text" class="el-form-item-title-btn" @click="rechargeDialogVisible = true">
          充值
        </el-button>
        <el-input type="text" disabled placeholder="加载中" :value="moneyFilter(sms.balance)"/>
      </el-form-item>

      <h2 class="title" style="margin-top: 48px">短信发送选项</h2>
      <el-form-item class="item-container">
        <el-switch
            v-model="form.sms_send_order"
            inactive-text="买家下单后, 发送卡密到买家手机号"
            :active-value="1"
            :inactive-value="0">
        </el-switch>
        <span class="tip" style="display: block; margin-top: -12px;">注意：需要买家额外填写收货手机号</span>
      </el-form-item>

      <el-form-item prop="sms_price" class="item-container">
        <span>短信单价</span>
        <el-input type="text" placeholder="请输入 API_KEY" v-model="form.sms_price"/>
      </el-form-item>

    </el-form>
    <div class="text-center" style="margin-top: 24px">
      <el-button @click="handleResetForm">刷新</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">保存更改</el-button>
    </div>

    <el-dialog
        title="短信账户充值"
        :visible.sync="rechargeDialogVisible"
        :close-on-click-modal="false">
      <el-form :model="rechargeForm" ref="form-recharge">
        <el-form-item prop="payway" class="item-container">
          <span style="display: block">请选择支付方式</span>
          <el-select v-model="rechargeForm.payway">
            <el-option label="支付宝" :value="isMobile?'alipaywap':'alipay'"/>
            <el-option label="微信" :value="isMobile?'wxwap':'wx'"/>
            <el-option label="手机QQ" :value="isMobile?'qqwap':'qq'"/>
          </el-select>
        </el-form-item>
        <el-form-item prop="amount" class="item-container"
                      :rules="[{ required: true, message: '请输入充值金额', trigger: 'change' },
                               { type: 'number', min: 1, message: '最小限制为 1.00, 请重新输入', trigger: 'change' },
                               { type: 'number', max: 1000000, message: '最大限制为 1000000.00, 请重新输入', trigger: 'change' }]">
          <span>请输入充值金额</span>
          <el-input type="number" v-model="rechargeForm.amount"
                    @input="v=>rechargeForm.amount=parseFloat(v)"></el-input>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="rechargeDialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="handleRecharge">确 定</el-button>
      </div>
    </el-dialog>
  </el-card>
</template>

<script>
  import NProgress from 'nprogress';
  import { sms } from '@/api/system';
  import { moneyFilter } from '~/utils/filters'

  export default {
    components: {},
    data () {
      const isMobile = this.$store.state.app.isMobile;
      return {
        isMobile,
        loading: false,
        labelWidth: '0px', 
        form: {
          sms_api_id: '',
          sms_api_key: '',
          sms_send_order: 0,
          sms_price: '',
        },
        sms: {
          buyLink: '',
          balance: ''
        },
        formRules: {},
        rechargeDialogVisible: false,
        rechargeForm: {
          payway: isMobile ? 'alipaywap' : 'alipay',
          amount: 10
        }
      }
    },
    computed: {
      a () {
        
        return decodeURIComponent('%68%74%74%70%73%3A%2F%2F%61%70%69%2E%68%69%73%2E%63%61%74%2F%61%70%69%2F%73%6D%73%2F%69%6E%66%6F');
      },
      b () {
        
        return decodeURIComponent('%68%74%74%70%73%3A%2F%2F%61%70%69%2E%68%69%73%2E%63%61%74%2F%61%70%69%2F%73%6D%73%2F%72%65%67%69%73%74%65%72');
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
      moneyFilter,
      getSet () {
        this.loading = true;
        sms().then(res => {
          Object.assign(this.form, res.data);
          if (this.form.sms_api_id && this.form.sms_api_key) {
            this.handleLoadSmsInfo()
          }
          this.form.sms_price = (this.form.sms_price / 100).toFixed(2);
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
          const data = Object.assign({}, this.form);
          data.sms_price = parseInt(data.sms_price * 100);
          sms(data).then(() => {
            this.loading = false;
            this.$notify({ title: '操作成功', message: '配置保存成功', type: 'success' });
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleApplySmsAccount () {
        this.loading = true;
        this.$http.post(this['b']).then(e => {
          this.form.sms_api_id = e.data.id;
          this.form.sms_api_key = e.data.key;
          Object.assign(this.sms, e.data);
          this.handleSubmit();
        }).finally(() => {
          this.loading = false;
        });
      },
      handleLoadSmsInfo () {
        this.$http.post(this['a'], { id: this.form.sms_api_id, key: this.form.sms_api_key }).then(e => {
          Object.assign(this.sms, e.data);
        }).catch(e => {
          this.sms.balance = e.message
        });
      },
      handleRecharge () {
        this.$refs['form-recharge'].validate(valid => {
          if (!valid) return;

          this.rechargeDialogVisible = false;
          window.open(`${this.sms.buyLink}/${parseInt(this.rechargeForm.amount * 100)}/${this.rechargeForm.payway}`, '_blank')

        });
      }
    }
  }
</script>


<style lang="scss" scoped>
  .el-form-item-title-btn {
    display: inline-block;
    margin-left: 4px
  }
</style>

