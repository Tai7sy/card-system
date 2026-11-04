<template>
<div :class="{'app-container': !inBox}">
  <h3 style="margin: 4px 0 28px 0;">添加卡密</h3>
  <el-form ref="form" :model="form" :label-width="labelWidth" :rules="formRules">
    <el-form-item label="类型">
      <el-radio-group v-model="form.type" size="small" @change="handleTypeChange">
        <el-radio-button :label="CARD_TYPE.TYPE_ONETIME">一次性</el-radio-button>
        <el-radio-button :label="CARD_TYPE.TYPE_REPEAT">重复销售</el-radio-button>
      </el-radio-group>
      <span style="font-size: 12px;display: block;line-height: 18px;" v-if="form.type===CARD_TYPE.TYPE_REPEAT"
            class="red-text">注意：将会限制商品最大购买量为1件</span>
    </el-form-item>

    <el-form-item label="商品" prop="product_id">
      <el-select v-model="form.category_id" filterable @change="getProducts" no-data-text="请先添加分类">
        <el-option v-for="item in categories" :value="item.id" :key="item.id" :label="item.name">
        </el-option>
      </el-select>
      <el-select v-model="form.product_id" filterable >
        <el-option v-for="item in products" :value="item.id" :key="item.id" :label="item.name">
        </el-option>
      </el-select>
    </el-form-item>

    <el-form-item label="卡密" prop="card">
      <el-input v-if="form.type === CARD_TYPE.TYPE_ONETIME"
                type="textarea" v-model="form.card" :autosize="{ minRows: 10, maxRows: 10}"
                :placeholder="'卡号+空格+密码，一行一张卡\n如：ABCDEFG 123456\n(卡密长度不得超过1024位)\n\n(建议每次添加不超过10000张)'"/>
      <el-input v-else
                type="text" v-model="form.card"
                :placeholder="'卡号+空格+密码'"/>
    </el-form-item>

    <el-form-item label="可售" prop="count_all" v-if="form.type === CARD_TYPE.TYPE_REPEAT"
                  :rules="[{ required: true, message: '请输入可售总次数', trigger: 'blur' },
                  { type: 'number', min: 1 ,max: 10000000, message: '可售总次数不能超过10000000'}]">
      <el-input type="number" placeholder="请输入可售总次数" v-model.number="form.count_all"/>
    </el-form-item>

    

    


    <el-form-item>
      <el-tooltip effect="dark" content="选中后表示重复的卡密将不会加入库存中" placement="top-start">
        <el-checkbox v-model="form.is_check">检查重复的卡密</el-checkbox>
      </el-tooltip>
    </el-form-item>


    <el-form-item>
      <el-button type="primary" @click="onSubmit" :loading="loading || uploading">添加</el-button>
      
    </el-form-item>
  </el-form>

  <div v-if="uploading" style="z-index: 1010" class="v-modal"></div>
  <div v-if="uploading" style="z-index: 1011" class="el-message-box__wrapper">
    <div style="position: relative;display: inline-block;width: 250px;
  background-color: #fff;border-radius: 4px;border: 1px solid #ebeef5;">
      <p>正在上传&nbsp;{{upload.now}}/{{upload.all}}</p>
    </div>
  </div>

</div>
</template>

<script>
import NProgress from 'nprogress'; // Progress 进度条
import { add as card_add, CARD_STATUS, CARD_TYPE } from '~/api/card';
import { getList as getCategoryList } from '~/api/category';
import { getList as getProductList } from '~/api/product';
export default {
  props: {
    inBox: {
      default: false
    },
    userInfo: {},
    category_id: {},
    product_id: {}
  },
  data() {
    return {
      loading: false,
      labelWidth: this.$store.state.app.isMobile ? '50px' : '90px',
      CARD_STATUS,
      CARD_TYPE,
      categories: [],
      products: [],
      form: {
        category_id: null,
        product_id: null,
        card: '',
        
        
        type: CARD_TYPE.TYPE_ONETIME,
        count_all: null,
        is_check: false
      },
      formRules: {
        product_id: [{
          required: true,
          message: '请选择商品',
          trigger: 'blur'
        }],
        card: [{
          required: true,
          message: '请输入卡密',
          trigger: 'blur'
        }]
      },
      upload: {
        now: 0,
        all: 0
      }
    };
  },
  computed: {
    uploading() {
      return this.upload.now < this.upload.all;
    }
  },
  watch: {
    loading(val, old) {
      if (val) {
        if (!old && !NProgress.isStarted()) NProgress.start();
      } else {
        NProgress.done();
      }
    }
  },
  mounted() {
    this.getCategories();
    if (this.userInfo) {
      this.form.user_id = this.userInfo.id;
    }
    if (+this.category_id > 0) {
      this.form.category_id = +this.category_id;
      if (+this.product_id > 0) {
        this.form.product_id = +this.product_id;
      }
    } else if (+this.$route.query.category_id > 0) {
      this.form.category_id = +this.$route.query.category_id;
      if (+this.$route.query.product_id > 0) {
        this.form.product_id = +this.$route.query.product_id;
      }
    }
  },
  methods: {
    getCategories() {
      this.categories = [];
      const where = {
        search: 'simple',
        val: 'list'
      };
      if (this.userInfo) {
        where.user_id = this.userInfo.id;
      }
      getCategoryList(where).then(ret => {
        this.categories = ret.data;
        if (!this.categories.length) {
          this.$alert('请先添加商品分类', '提示', {
            confirmButtonText: '去添加'
          }).then(() => {
            this.$router.push({
              path: '/admin/product/category',
              query: {
                action: 'create'
              }
            });
          });
        } else {
          if (this.categories.length === 1) {
            this.form.category_id = this.categories[0].id;
          }
          if (this.form.category_id > 0) {
            this.getProducts(this.form.category_id);
          }
        }
      });
    },
    getProducts(category_id) {
      if (this.category_id_old === category_id) return;
      this.category_id_old = category_id;
      this.products = [];
      const where = {
        search: 'simple',
        val: category_id,
        from: 'card'
      };
      if (this.userInfo) {
        where.user_id = this.userInfo.id;
      }
      getProductList(where).then(ret => {
        this.products = ret.data;
        if (!this.products.length) {
          this.$alert('请先添加商品', '提示', {
            confirmButtonText: '去添加'
          }).then(() => {
            this.$router.push({
              path: '/admin/product/add',
              query: {
                action: 'create'
              }
            });
          });
        } else {
          if (this.products.length === 1) {
            this.form.product_id = this.products[0].id;
          }
        }
      });
    },
    handleTypeChange() {
      if (this.form.type === CARD_TYPE.TYPE_REPEAT) {
        this.form.card = '';
      }
    },
    onSubmit() {
      this.$refs['form'].validate(valid => {
        if (!valid) return;

        
        
        

        if (this.form.type === CARD_TYPE.TYPE_REPEAT) {
          if (this.form.card.indexOf('\n') > -1 || this.form.card.indexOf('\r') > -1) {
            return this.$message({
              message: '重复销售的卡密每次只能添加一张',
              type: 'error'
            });
          }
          this.upload.now = 0;
          this.upload.all = 1;
          card_add(this.form).then(() => {
            this.upload.now = 1;
            this.$alert('添加卡密结束', '提示', {
              type: 'success'
            }).then(() => {
              this.$emit('submit', true);
            });
          }).catch(() => {
            this.upload.now = 1;
          });
        } else {
          const card_arr = this.form.card.split('\n').filter(e => e.trim().length > 0);
          this.upload.now = 0;
          this.upload.all = card_arr.length;
          if (this.upload.all >= 100000) {
            this.$alert('单次添加不能超过100000张', '提示', {
              type: 'error'
            });
            return;
          }
          const per_round = 1000; // 每次上传多少张
          const rounds = Math.ceil(card_arr.length / per_round);
          const upload_func = round => {
            if (round >= rounds) {
              this.$alert('添加卡密结束', '提示', {
                type: 'success'
              }).then(() => {
                this.$emit('submit', true);
              });
              return;
            }
            const cards = card_arr.slice(round * per_round, (round + 1) * per_round);
            const submitInfo = Object.assign({}, this.form);
            submitInfo.card = cards.join('\n');
            card_add(submitInfo).then(() => {
              setTimeout(() => {
                this.upload.now += cards.length;
                upload_func(round + 1);
              }, 100);
            }).catch(e => {
              this.upload.now += cards.length;
              this.$notify({
                title: '提示',
                message: `添加卡密第${round}轮结果未知`,
                type: 'info',
                duration: 3000
              });
              try {
                eval('console.log(' + e + ')');
              } catch (e) {
                console.log(e);
              }
              setTimeout(() => {
                upload_func(round + 1);
              }, 100);
            });
          };
          upload_func(0);
        }
      });
    }
  }
};
</script>
