<template>
  <div :class="{'app-container': !inBox}">
    <h3 style="margin: 4px 0 28px 0;">{{ isAdd ? '添加商品' : '编辑商品' }}</h3>

    <el-form ref="form" :model="form" :label-position="isMobile?'top':''" label-width="90px"
             :rules="formRules">

      <el-form-item label="分类" prop="category_id" :class="isMobile?'item-container':''"
                    :style="isMobile?'margin-top: -32px':''">
        <el-select v-model="form.category_id" no-data-text="请先添加分类">
          <el-option v-for="item in categories" :value="item.id" :key="item.id" :label="item.name"/>
        </el-select>
      </el-form-item>


      <el-form-item label="名称" prop="name" :class="isMobile?'item-container':''">
        <el-input type="text" placeholder="商品名称，如：91游戏30元点卡" v-model="form.name"/>
      </el-form-item>

      <el-form-item label="排序" prop="sort" :class="isMobile?'item-container':''">
        <el-tooltip effect="dark" content="数字越小越靠前" placement="top-start">
          <el-input-number controls-position="right" :min="0" :max="10000000" v-model="form.sort" size="small"/>
        </el-tooltip>
      </el-form-item>


      <el-form-item label="商品描述" prop="description" :class="isMobile?'item-container':''">
        <quill-editor ref="editor-description" :user-info="userInfo_"
                      placeholder="本图文说明将显示在商品购买页面"/>
      </el-form-item>

      <el-form-item label="使用说明" prop="instructions" :class="isMobile?'item-container':''">
        <quill-editor ref="editor-instructions" :user-info="userInfo_"
                      placeholder="本图文说明将显示在订单查询网站且购买成功的订单中，一般设置详细使用说明，售后QQ群，或者下载地址类"/>
      </el-form-item>

      <el-form-item label="库存预警" prop="count_warn" :class="isMobile?'item-container':''">
        <el-tooltip effect="dark" content="到达预警值后会发送通知邮件，填0不预警" placement="top-start">
          <el-input-number controls-position="right" :min="0" :max="10000000" v-model="form.count_warn" size="small"/>
        </el-tooltip>

      </el-form-item>

      <el-form-item label="成本价" prop="cost" :class="isMobile?'item-container':''">
        <el-tooltip effect="dark" content="成本价用于商户后台计算利润" placement="top-start">
          <el-input type="number" placeholder="请输入商品成本价" v-model.number="form.cost"/>
        </el-tooltip>
      </el-form-item>

      <el-form-item label="售价" prop="price" :class="isMobile?'item-container':''">
        <el-input type="number" placeholder="请输入商品售价" v-model.number="form.price"/>
      </el-form-item>

      <el-form-item label="批发价" prop="price_whole" class="price-whole" :class="isMobile?'item-container':''">
        <div :style="{'line-height':isMobile?'18px':'inherit'}">
          <a @click="price_whole.push([1,form.price])">添加</a>
          <div class="price-body">
            <div v-for="(item,index) in price_whole">
              <label>件数
                <input type="number" min="1" max="10000" v-model="item[0]"/>
              </label>
              <label>价格
                <input type="number" min="0.01" max="10000000" v-model="item[1]"/>
              </label>
              <a style="cursor: pointer;" @click="price_whole.splice(index,1)">删除</a>
            </div>
          </div>
        </div>
      </el-form-item>


      <el-form-item label="优惠券" prop="support_coupon" :class="isMobile?'item-container':''">
        <el-checkbox v-model="form.support_coupon">支持</el-checkbox>
      </el-form-item>

      <el-form-item label="购买限制" prop="buy_min" :class="isMobile?'item-container':''">
        <el-tooltip effect="dark" content="最小购买量" placement="top-start">
          <el-input-number controls-position="right" :min="1" :max="10000" v-model="form.buy_min" size="small"/>
        </el-tooltip>
        <el-tooltip effect="dark" content="最大购买量" placement="top-start">
          <el-input-number controls-position="right" :min="1" :max="10000" v-model="form.buy_max" size="small"/>
        </el-tooltip>
      </el-form-item>


      <el-form-item label="购买密码" prop="password" class="buy-password" :class="isMobile?'item-container':''">
        <el-tooltip effect="dark" content="设置商品购买密码，开启后只有输入密码才能购买" placement="top-start">
          <el-input type="text" placeholder="购买密码(可选)" v-model="form.password">
            <el-checkbox slot="prepend" v-model="form.password_open">开启</el-checkbox>
          </el-input>
        </el-tooltip>
      </el-form-item>


      <el-form-item label="状态" prop="enabled" :class="isMobile?'item-container':''"
                    :style="isMobile?'margin-bottom:22px':''">
        <el-radio-group v-model="form.enabled" size="small">
          <el-radio-button :label="1">上架</el-radio-button>
          <el-radio-button :label="0" class="el-radio-gray">下架</el-radio-button>
        </el-radio-group>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="handleSubmit" :loading="loading">{{isAdd?'添加':'编辑'}}</el-button>
      </el-form-item>
    </el-form>

  </div>
</template>
<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import { edit as product_edit } from '@/api/product'
  import { getList as getCategoryList } from '@/api/category'
  import QuillEditor from '@/components/QuillEditor';
  export default {
    components: { QuillEditor },
    props: {
      info: {},
      inBox: {
        default: false
      },
      userInfo: {}
    },
    data () {
      return {
        loading: false,
        isMobile: this.$store.state.app.isMobile,
        userInfo_: null,
        upload_url: window.API_HOST + 'product/upload',
        form: {
          id: null,
          category_id: null,
          name: '',
          description: '',
          instructions: '',
          sort: 1000,
          buy_min: 1,
          buy_max: 10,
          count_warn: '',
          support_coupon: false,
          password: '',
          password_open: false,
          cost: 0,
          price: 0,
          price_whole: '',
          enabled: null,
        },
        price_whole: [],
        categories: [],
        formRules: {
          category_id: [
            { required: true, message: '请选择商品分类', trigger: 'blur' },
          ],
          name: [
            { required: true, message: '请输入商品名称', trigger: 'blur' },
          ],
          cost: [
            { type: 'number', min: 0, message: '不需要请填0', trigger: 'blur' },
            { type: 'number', min: 0, max: 10000000, message: '成本价不能超过10000000' }
          ],
          price: [
            { required: true, message: '请输入商品售价', trigger: 'blur' },
            { type: 'number', min: 0.01, message: '商品售价不能低于0.01' },
            { type: 'number', max: 10000000, message: '商品售价不能超过10000000' }
          ],
          enabled: [
            { required: true, message: '请选择状态', trigger: 'blur' },
          ],
        },
      }
    },
    computed: {
      isAdd () {
        return !this.info
      }
    },
    watch: {
      loading (val, old) {
        this.$emit('update:loading', val);
        if (val) {
          if (!old && !NProgress.isStarted()) NProgress.start();
        } else {
          NProgress.done();
        }
      }
    },
    mounted () {
      if (this.userInfo) {
        this.userInfo_ = this.userInfo;
      } else {
        this.userInfo_ = this.$store.state.user.userInfo;
      }
      this.getCategories();
      if (!this.isAdd) {
        for (const key in this.form) {
          if (this.form.hasOwnProperty(key)) this.$set(this.form, key, this.info[key]);
        }
        this.form.cost = (this.info.cost / 100).toFixed(2) * 1.0;
        this.form.price = (this.info.price / 100).toFixed(2) * 1.0;
        this.form.support_coupon = this.info.support_coupon === 1;
        this.form.password_open = this.info.password_open === 1;
        try {
          this.price_whole = JSON.parse(this.form.price_whole).map(e => [e[0], (e[1] / 100).toFixed(2)]);
        } catch (e) {
          this.price_whole = [];
        }
        if (this.price_whole == null) this.price_whole = [];
      }
      if (this.userInfo) {
        this.form.user_id = this.userInfo.id;
      }
      if (!this.isAdd) {
        this.$refs['editor-description'].setValue(this.info.description);
        this.$refs['editor-instructions'].setValue(this.info.instructions);
      }
    },
    methods: {
      getCategories () {
        const where = { search: 'simple', val: 'list', };
        if (this.userInfo) {
          where.user_id = this.userInfo.id;
        }
        getCategoryList(where).then(ret => {
          this.categories = ret.data;
        })
      },
      handleSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;
          const price_whole = this.price_whole.map(e => [e[0], parseInt(e[1] * 100)]);
          this.form.price_whole = JSON.stringify(price_whole);

          this.form.description = this.$refs['editor-description'].getValue();
          this.form.instructions = this.$refs['editor-instructions'].getValue();

          product_edit(this.form).then(() => {
            this.loading = false;
            this.$notify({
              title: '提示',
              message: (this.isAdd ? '添加' : '编辑') + '商品成功',
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
  }
</script>

<style lang="scss">
  @import "../edit";
</style>
