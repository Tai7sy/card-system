<template>
  <div :class="{'app-container': !inBox}">
    <h3 style="margin: 4px 0 28px 0;">生成优惠券</h3>
    <el-form ref="form" :model="form" :label-width="labelWidth" :rules="formRules">
      <el-form-item label="类型">
        <el-radio-group v-model="form.type" size="small" @change="onTypeChange">
          <el-radio-button :label="couponType.TYPE_ONETIME">一次性</el-radio-button>
          <el-radio-button :label="couponType.TYPE_REPEAT">重复使用</el-radio-button>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="商品" prop="product_id">
        <el-select v-model="form.category_id" @change="getProducts" no-data-text="请先添加分类">
          <el-option label="全部" :value="-1"/>
          <el-option v-for="item in categories" :value="item.id" :key="item.id" :label="item.name">
          </el-option>
        </el-select>
        <el-select v-model="form.product_id">
          <el-option label="全部" :value="-1"/>
          <el-option v-for="item in products" :value="item.id" :key="item.id" :label="item.name">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item label="面额" prop="discount_val">
        <el-input type="number" placeholder="请输入优惠券面额" v-model.number="form.discount_val">
          <el-select v-model="form.discount_type" slot="append" placeholder="类型" value="" @change="onDiscountTypeChange"
                     size="small" style="width:70px">
            <el-option label="￥" :value="couponDiscountType.AMOUNT"/>
            <el-option label="%" :value="couponDiscountType.PERCENT"/>
          </el-select>
        </el-input>
      </el-form-item>

      <el-form-item label="可用" prop="count_all" v-if="form.type === couponType.TYPE_REPEAT"
                    :rules="[{ required: true, message: '请输入可用次数', trigger: 'blur' },
                    { type: 'number', min: 1 ,max: 10000000, message: '可用次数不能超过10000000'}]">
        <el-input type="number" placeholder="请输入可用次数" v-model.number="form.count_all">
        </el-input>
      </el-form-item>

      <el-form-item label="数量" prop="count" v-if="form.type === couponType.TYPE_ONETIME"
                    :rules="[{ required: true, message: '请输入生成数量', trigger: 'blur' },
                    { type: 'number', min: 1 ,max: 100, message: '每次生成不能大于100张'}]">
        <el-input type="number" placeholder="请输入生成数量" v-model.number="form.count">
        </el-input>
      </el-form-item>


      <el-form-item label="券" prop="coupon" v-if="form.type === couponType.TYPE_REPEAT">
        <el-input type="text" placeholder="为空系统自动生成" v-model="form.coupon">
        </el-input>
      </el-form-item>

      <el-form-item label="过期" prop="expire_at">
        <el-date-picker v-model="form.expire_at" type="datetime" placeholder="选择过期时间">
        </el-date-picker>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="onSubmit" :loading="loading">添加</el-button>
      </el-form-item>
    </el-form>

    <div v-if="list.length" style="font-size: 14px;color: #606266;line-height: 16px;margin-top: 24px"
         :style="{'margin-left': labelWidth}">
      <span>生成优惠券&nbsp;{{list.length}}张</span>
      <p v-html="list.join('<br>')"></p>
    </div>

  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import { couponType, couponDiscountType, create } from 'api/coupon'
  import { getList as getCategoryList } from 'api/category'
  import { getList as getProductList } from 'api/product'

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
        labelWidth: this.$store.state.app.isMobile ? '50px' : '90px',
        couponType, couponDiscountType,
        categories: [],
        products: [],
        form: {
          category_id: -1,
          product_id: -1,
          type: couponType.TYPE_ONETIME,
          coupon: null,
          discount_val: '',
          discount_type: couponDiscountType.AMOUNT,
          count: null, // 生成数量
          expire_at
        },
        formRules: {
          discount_val: [
            { required: true, message: '请输入优惠券面额', trigger: 'blur' },
            { type: 'number', min: 0.01, max: 1000000, message: '数字需要在0.01-1000000之间' }
          ]
        },
        list: []
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
      onTypeChange () {
      },
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
      onSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.list = [];
          this.loading = true;
          const info = Object.assign({}, this.form);
          if (info.discount_type === couponDiscountType.AMOUNT) info.discount_val *= 100;
          if (info.expire_at) info.expire_at = info.expire_at.format('yyyy-MM-dd');
          create(info).then(res => {
            this.loading = false;
            this.list = res.data;
            this.$notify({ title: '提示', message: '生成优惠券成功', type: 'success', duration: 3000 });
            this.$emit('submit', true);
          }).catch(() => {
            this.loading = false;
          });
        });
      }
    }
  }
</script>
