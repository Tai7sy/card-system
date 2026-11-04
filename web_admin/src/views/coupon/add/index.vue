<template>
  <div :class="{'app-container': !inBox}">
    <h3 style="margin: 4px 0 28px 0;">生成优惠券</h3>
    <el-form ref="form" :model="form"
             :label-position="isMobile?'top':''"
             label-width="90px"
             :rules="formRules">

      <el-form-item label="类型" :class="formItemClass"
                    :style="isMobile?'margin-top: -24px':''">
        <el-radio-group v-model="form.type" size="small">
          <el-radio-button :label="COUPON_TYPE.TYPE_ONETIME">一次性</el-radio-button>
          <el-radio-button :label="COUPON_TYPE.TYPE_REPEAT">重复使用</el-radio-button>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="商品" prop="product_id" :class="formItemClass">
        <el-select v-model="form.category_id" filterable @change="getProducts">
          <el-option label="全部" :value="-1"/>
          <el-option v-for="item in categories" :value="item.id" :key="item.id" :label="item.name">
          </el-option>
        </el-select>
        <el-select v-model="form.product_id" filterable>
          <el-option label="全部" :value="-1"/>
          <el-option v-for="item in products" :value="item.id" :key="item.id" :label="item.name">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item label="面额" prop="discount_val" :class="formItemClass" :rules="discountRules">
        <el-input type="text" placeholder="请输入优惠券面额" v-model="form.discount_val"
                  @change="v=>form.discount_val = +v||0">
          <el-select v-model="form.discount_type" slot="append" placeholder="类型" value=""
                     size="small" style="width:70px">
            <el-option label="￥" :value="COUPON_DISCOUNT_TYPE.AMOUNT"/>
            <el-option label="%" :value="COUPON_DISCOUNT_TYPE.PERCENT"/>
          </el-select>
        </el-input>
        <div class="el-form-item__tip">
          <span>* 请合理设置优惠金额, 以避免产生亏损</span>
        </div>
      </el-form-item>

      <el-form-item label="可用次数" prop="count_all" v-if="form.type === COUPON_TYPE.TYPE_REPEAT"
                    :rules="[{ required: true, message: '请输入可用次数', trigger: 'blur' },
                    { type: 'number', min: 1 ,max: 10000000, message: '可用次数不能超过10000000'}]"
                    :class="formItemClass">
        <el-input type="number" placeholder="请输入可用次数" v-model.number="form.count_all">
        </el-input>
      </el-form-item>

      <el-form-item label="生成数量" prop="count" v-if="form.type === COUPON_TYPE.TYPE_ONETIME"
                    :rules="[{ required: true, message: '请输入生成数量', trigger: 'blur' },
                    { type: 'number', min: 1 ,max: 100, message: '每次生成不能大于100张'}]"
                    :class="formItemClass">
        <el-input type="number" placeholder="请输入生成数量" v-model.number="form.count">
        </el-input>
      </el-form-item>


      <el-form-item label="券代码" prop="coupon" v-if="form.type === COUPON_TYPE.TYPE_REPEAT"
                    :class="formItemClass">
        <el-input type="text" placeholder="为空系统自动生成" v-model="form.coupon">
        </el-input>
      </el-form-item>

      <el-form-item label="过期时间" prop="expire_at" :class="formItemClass">
        <el-date-picker v-model="form.expire_at" type="datetime" placeholder="选择过期时间">
        </el-date-picker>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="onSubmit" :loading="loading">添加</el-button>
      </el-form-item>
    </el-form>

    <div v-if="list.length" style="font-size: 14px;color: #606266;line-height: 16px;margin-top: 24px"
         :style="{'margin-left': isMobile ? 0: '90px'}">
      <span>生成优惠券&nbsp;{{list.length}}张</span>
      <el-button ref="copy-coupons"
                 size="mini" style="display: inline-block; margin-left: 12px"
                 v-clipboard="list.join('\r\n')"
                 @success="$message({message: '复制成功',type: 'success', duration: 1000})"
                 @error="$message({message: '复制失败, 请手动复制', type: 'error', duration: 1000})">
        一键复制
      </el-button>
      <p v-html="list.join('<br>')"></p>
    </div>

  </div>
</template>

<script>
  import NProgress from 'nprogress'
  import { COUPON_TYPE, COUPON_DISCOUNT_TYPE, create } from '~/api/coupon'
  import { getList as getCategoryList } from '~/api/category'
  import { getList as getProductList } from '~/api/product'
  import { htmlspecialchars } from '~/utils/helper'

  export default {
    props: {
      inBox: {
        default: false
      },
      userInfo: {}
    },
    data () {
      const expire_at = new Date();
      expire_at.setTime(expire_at.getTime() + 3600 * 1000 * 24 * 7);
      return {
        loading: false,
        isMobile: this.$store.state.app.isMobile,
        COUPON_TYPE, COUPON_DISCOUNT_TYPE,
        categories: [],
        products: [],
        form: {
          category_id: -1,
          product_id: -1,
          type: COUPON_TYPE.TYPE_ONETIME,
          coupon: null,
          discount_val: '',
          discount_type: COUPON_DISCOUNT_TYPE.AMOUNT,
          count: null, // 生成数量
          expire_at
        },
        formRules: {
          discount_val: []
        },
        list: []
      }
    },
    computed: {
      discountRules() {
        if (this.form.discount_type === COUPON_DISCOUNT_TYPE.AMOUNT) {
          return [
            { required: true, message: '请输入优惠券面额', trigger: 'change' },
            { type: 'number', min: 0.01, max: 10000000, message: '数字需要在0.01-10000000之间', trigger: 'change' }
          ];
        } else if (this.form.discount_type === COUPON_DISCOUNT_TYPE.PERCENT) {
          return [
            { required: true, message: '请输入优惠百分比', trigger: 'change' },
            { type: 'number', min: 1, max: 100, message: '数字需要在1-100之间', trigger: 'change' }
          ];
        }
        return []
      },
      formItemClass () {
        return this.isMobile ? 'coupon-form-item' : '';
      },
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
      this.getCategories();
      if (this.userInfo) {
        this.form.user_id = this.userInfo.id;
      }
    },
    methods: {
      getCategories () {
        this.categories = [];
        const where = { search: 'simple', val: 'list', };
        if (this.userInfo) {
          where.user_id = this.userInfo.id;
        }
        getCategoryList(where).then(ret => {
          this.categories = ret.data;
        })
      },
      getProducts (category_id) {
        if (this.category_id_old === category_id) return;
        this.category_id_old = category_id;
        this.products = [];
        const where = { search: 'simple', val: category_id };
        if (this.userInfo) {
          where.user_id = this.userInfo.id;
        }
        getProductList(where).then(ret => {
          this.products = ret.data;
        })
      },
      onSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.list = [];
          this.loading = true;
          const info = Object.assign({}, this.form);
          if (info.discount_type === COUPON_DISCOUNT_TYPE.AMOUNT) info.discount_val *= 100;
          if (info.expire_at) info.expire_at = info.expire_at.format('yyyy-MM-dd');
          create(info).then(res => {
            this.loading = false;
            this.list = res.data;
            const lists = this.list.join('\r\n');
            this.$msgbox({
              title: '生成优惠券成功',
              message: '优惠券列表：<br><textarea title="优惠券列表" rows="6" style="width: 100%" readonly>'
                + htmlspecialchars(lists) + '</textarea>',
              showCancelButton: true,
              dangerouslyUseHTMLString: true,
              cancelButtonText: '一键复制',
            }).catch(action => {
              if (action === 'cancel') {
                this.$refs['copy-coupons'].$el.click();
              }
            });
            this.$emit('submit', true);
          }).catch(() => {
            this.loading = false;
          });
        });
      }
    }
  }
</script>


<style lang="scss">
  .coupon-form-item {
    .el-form-item__label {
      padding-bottom: 0 !important;
      margin-bottom: -4px !important;
    }

    margin-bottom: 8px;

    .el-select, .el-checkbox {
      display: block;
    }

    .el-checkbox + .el-checkbox {
      margin-left: 0;
    }

    .el-radio-group {
      display: block;
    }
  }
</style>
