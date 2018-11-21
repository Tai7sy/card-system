<template>
  <el-dialog title="编辑卡号" :visible.sync="visible">
    <el-form ref="form" :model="form" label-width="80px" :rules="formRules">
      <el-form-item label="优惠券" prop="coupon">
        <el-input type="text" placeholder="请输入优惠券" v-model="form.coupon">
        </el-input>
      </el-form-item>

      <!--el-form-item label="状态">
        <el-radio-group v-model="form.status" size="small">
          <el-radio-button :label="couponStatus.STATUS_NORMAL">未使用</el-radio-button>
          <el-radio-button :label="couponStatus.STATUS_USED" class="el-radio-gray">已使用</el-radio-button>
        </el-radio-group>
      </el-form-item-->

      <el-form-item label="面额" prop="discount_val">
        <el-input type="number" placeholder="请输入优惠券面额" v-model.number="form.discount_val">
          <el-select v-model="form.discount_type" slot="append" placeholder="类型" value="" @change="onDiscountTypeChange"
                     size="small" style="width:70px">
            <el-option label="￥" :value="couponDiscountType.AMOUNT"/>
            <el-option label="%" :value="couponDiscountType.PERCENT"/>
          </el-select>
        </el-input>
      </el-form-item>

      <el-form-item label="类型">
        <el-radio-group v-model="form.type" size="small">
          <el-radio-button :label="couponType.TYPE_ONETIME">一次性</el-radio-button>
          <el-radio-button :label="couponType.TYPE_REPEAT">重复使用</el-radio-button>
        </el-radio-group>
      </el-form-item>


      <el-form-item label="可用" prop="count_all" v-if="form.type === couponType.TYPE_REPEAT"
                    :rules="[{ required: true, message: '请输入可用次数', trigger: 'blur' },
                    { type: 'number', min: 1 ,max: 10000000, message: '可用次数不能超过10000000'}]">
        <el-input type="number" placeholder="请输入可用次数" v-model.number="form.count_all">
        </el-input>
        <span>当前已使用&nbsp;{{form.count_used}}次</span>
      </el-form-item>

      <el-form-item label="过期" prop="expire_at">
        <el-date-picker v-model="form.expire_at" type="datetime" placeholder="选择过期时间">
        </el-date-picker>
      </el-form-item>

    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">确认编辑
      </el-button>
    </div>
  </el-dialog>
</template>

<script>
  import { couponStatus, couponType, couponDiscountType, edit as coupon_edit } from 'api/coupon'

  export default {
    props: {
      info: {}
    },
    data () {
      return {
        loading: false,
        visible: true,
        couponStatus, couponType, couponDiscountType,
        form: {
          id: 0,
          product_id: 0,
          coupon: '',
          // status: couponStatus.STATUS_NORMAL,
          type: couponType.TYPE_ONETIME,
          discount_val: '',
          discount_type: couponDiscountType.AMOUNT,
          count_all: null,
          count_used: null,
          expire_at: null,
        },
        formRules: {
          coupon: [
            { required: true, message: '请输入卡号', trigger: 'blur' },
          ],
          discount_val: [
            { required: true, message: '请输入优惠券面额', trigger: 'blur' },
          ]
        }
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
      this.form.id = this.info.id;
      this.form.coupon = this.info.coupon;
      this.form.status = this.info.status;
      this.form.discount_type = this.info.discount_type;
      if (this.info.discount_type === couponDiscountType.AMOUNT) {
        this.form.discount_val = (this.info.discount_val / 100).toFixed(2);
      } else {
        this.form.discount_val = this.info.discount_val;
      }
      this.form.type = this.info.type;
      this.form.count_all = this.info.count_all;
      this.form.count_used = this.info.count_used;
      this.form.expire_at = new Date(this.info.expire_at);
    },
    methods: {
      onDiscountTypeChange () {
        if (this.form.discount_type === couponDiscountType.AMOUNT) {
          this.formRules.discount_val = [
            { required: true, message: '请输入优惠券面额', trigger: 'blur' },
            { type: 'number', min: 0.01, max: 10000000, message: '数字需要在0.01-10000000之间' }
          ];
        } else if (this.form.discount_type === couponDiscountType.PERCENT) {
          this.formRules.discount_val = [
            { required: true, message: '请输入优惠百分比', trigger: 'blur' },
            { type: 'number', min: 1, max: 100, message: '数字需要在1-100之间' }
          ];
        }
      },
      handleSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;
          const info = Object.assign({}, this.form);
          if (info.discount_type === couponDiscountType.AMOUNT) info.discount_val *= 100;
          if (info.expire_at) info.expire_at = info.expire_at.format('yyyy-MM-dd');
          coupon_edit(info).then(() => {
            this.loading = false;
            this.$notify({
              title: '提示',
              message: '编辑优惠券成功',
              type: 'success', duration: 3000
            });
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
