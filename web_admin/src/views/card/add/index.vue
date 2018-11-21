<template>
  <div :class="{'app-container': !inBox}">
    <h3 style="margin: 4px 0 28px 0;">添加卡密</h3>
    <el-form ref="form" :model="form" :label-width="labelWidth" :rules="formRules">
      <el-form-item label="类型">
        <el-radio-group v-model="form.type" size="small" @change="handleTypeChange">
          <el-radio-button :label="cardType.TYPE_ONETIME">一次性</el-radio-button>
          <el-radio-button :label="cardType.TYPE_REPEAT">重复使用</el-radio-button>
        </el-radio-group>
        <span style="font-size: 12px;display: block;line-height: 18px;" v-if="form.type===cardType.TYPE_REPEAT"
              class="red-text">注意：将会限制商品最大购买量为1件</span>
      </el-form-item>

      <el-form-item label="商品" prop="product_id">
        <el-select v-model="form.category_id" @change="getProducts" no-data-text="请先添加分类">
          <el-option v-for="item in categories" :value="item.id" :key="item.id" :label="item.name">
          </el-option>
        </el-select>
        <el-select v-model="form.product_id">
          <el-option v-for="item in products" :value="item.id" :key="item.id" :label="item.name">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item label="卡密" prop="card">
        <el-input v-if="form.type === cardType.TYPE_ONETIME"
                  type="textarea" v-model="form.card" :autosize="{ minRows: 10, maxRows: 10}"
                  :placeholder="'卡号+空格+密码，一行一张卡\n如：ABCDEFG 123456\n(卡密长度不得超过100位)'"/>
        <el-input v-else
                  type="text" v-model="form.card"
                  :placeholder="'卡号+空格+密码'"/>
      </el-form-item>

      <el-form-item label="可售" prop="count_all" v-if="form.type === cardType.TYPE_REPEAT"
                    :rules="[{ required: true, message: '请输入可售总次数', trigger: 'blur' },
                    { type: 'number', min: 1 ,max: 10000000, message: '可售总次数不能超过10000000'}]">
        <el-input type="number" placeholder="请输入可售总次数" v-model.number="form.count_all"/>
      </el-form-item>

      <!--el-form-item label="分隔" prop="delimiter">
        <el-select v-model="form.delimiter" placeholder="请选择">
          <el-option label="换行符" :value="'\n'"/>
          <el-option label="换行符" :value="'\n'"/>
          <el-option label="换行符" :value="'\n'"/>
        </el-select>
      </el-form-item-->

      <!--el-form-item label="状态">
        <el-radio-group v-model="form.status" size="small">
          <el-radio-button :label="cardStatus.STATUS_NORMAL">未使用</el-radio-button>
          <el-radio-button :label="cardStatus.STATUS_SOLD" class="el-radio-gray">已使用</el-radio-button>
        </el-radio-group>
      </el-form-item-->


      <el-form-item>
        <el-tooltip effect="dark" content="选中后表示重复的卡密将不会加入库存中" placement="top-start">
          <el-checkbox v-model="form.is_check">检查重复的卡密</el-checkbox>
        </el-tooltip>
      </el-form-item>


      <el-form-item>
        <el-button type="primary" @click="onSubmit" :loading="loading || uploading">添加</el-button>
        <p class="red-text">注意：当前版本最多可以添加 <b>10000</b> 条卡密记录（不包含已售出）（重复使用类型只算1条），<br>
          超出限制会添加失败，请合理配置商品库存。</p>
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
  import { add as card_add, cardStatus, cardType } from 'api/card'
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
      return {
        loading: false,
        labelWidth: this.$store.state.app.isMobile ? '50px' : '90px',
        cardStatus, cardType,
        categories: [],
        products: [],
        form: {
          category_id: null,
          product_id: null,
          card: '',
          // delimiter: '\n',
          // status: cardStatus.STATUS_NORMAL,
          type: cardType.TYPE_ONETIME,
          count_all: null,
          is_check: false,
        },
        formRules: {
          product_id: [
            { required: true, message: '请选择商品', trigger: 'blur' },
          ],
          card: [
            { required: true, message: '请输入卡密', trigger: 'blur' },
          ]
        },
        upload: {
          now: 0,
          all: 0
        }
      }
    },
    computed: {
      uploading () {
        return this.upload.now < this.upload.all;
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
      handleTypeChange () {
        if (this.form.type === cardType.TYPE_REPEAT) {
          this.form.card = '';
        }
      },
      onSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;

          if (this.form.card.indexOf('<') > -1) {
            return this.$message({ message: '卡密不能包含 < ', type: 'error' });
          }

          if (this.form.type === cardType.TYPE_REPEAT) {
            if (this.form.card.indexOf('\n') > -1 || this.form.card.indexOf('\r') > -1) {
              return this.$message({ message: '重复使用的卡密每次只能添加一张', type: 'error' });
            }
            this.upload.now = 0;
            this.upload.all = 1;
            card_add(this.form).then(() => {
              this.upload.now = 1;
              this.$alert('添加卡密结束', '提示', { type: 'success' }).then(() => {
                this.$emit('submit', true);
              });
            }).catch(() => {
              this.upload.now = 1;
            });
          } else {
            const card_arr = this.form.card.split('\n').filter(e => e.trim().length > 0);
            this.upload.now = 0;
            this.upload.all = card_arr.length;
            const per_round = 50; // 每次上传多少张
            const rounds = Math.ceil(card_arr.length / per_round);
            const upload_func = round => {
              if (round >= rounds) {
                this.$alert('添加卡密结束', '提示', { type: 'success' }).then(() => {
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
                }, 1000);
              }).catch(e => {
                this.upload.now += cards.length;
                this.$notify({ title: '提示', message: `添加卡密第${round}轮结果未知`, type: 'info', duration: 3000 });
                try {
                  eval('console.log(' + e + ')');
                } catch (e) {
                  console.log(e);
                }
                setTimeout(() => {
                  upload_func(round + 1);
                }, 1000);
              });
            };

            upload_func(0);
          }
        });
      }
    }
  }
</script>
