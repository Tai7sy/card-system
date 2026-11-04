<template>
<div :class="{'app-container': !inBox}">
  <h3 style="margin: 4px 0 28px 0;">{{ isAdd ? '添加商品' : '编辑商品' }}
    
  </h3>

  <el-alert type="info" title="" v-if="isFromRetail" style="margin-bottom: 36px;" :closable="false">
    <div class="retail-root-info">
      当前商品为分销商品：
      <el-button v-if="retail_from" type="text" style="padding: 4px;"
                 @click="showRetailFromInfo=!showRetailFromInfo">
        查看货源
      </el-button>
      <span v-else style="color: red">供货商品已删除</span>
      <div style="margin-top: 12px" v-if="showRetailFromInfo">
        <p>商品名称:&nbsp;{{retail_from.name}}</p>
        <p>商品分类:&nbsp;{{retail_from.category ? retail_from.category.name : '?未知?'}}</p>
        <p>商品介绍:&nbsp;
          <span style="border: 1px solid #eee;display: block" v-html="renderDelta(retail_from.description)"></span>
        </p>
        <p>进货价格:&nbsp;￥{{(retail_from.retail_options.price / 100).toFixed(2)}}</p>
      </div>
    </div>
  </el-alert>

  <el-form ref="form" :model="form"
           :label-position="isMobile?'top':''"
           label-width="90px"
           :rules="formRules"
           v-loading="loading">

    <el-form-item label="分类" prop="category_id" :class="formItemClass"
                  :style="isMobile?'margin-top: -24px':''">
      <el-select v-model="form.category_id"
                 allow-create filterable
                 no-data-text="请输入分类名称"
                 placeholder="请选择商品分类"
                 style="width: 100%">
        <el-option v-for="item in categories" :value="item.id" :key="item.id" :label="item.name"/>
      </el-select>
    </el-form-item>

    <el-form-item label="名称" prop="name" :class="formItemClass">
      <el-input type="text" placeholder="商品名称，如：91游戏30元点卡" v-model="form.name"
                :disabled="isFromRetail && retail_from && !retail_from.retail_options.can_edit"
      />
      <div class="el-form-item__tip"
           v-if="isFromRetail && retail_from && !retail_from.retail_options.can_edit">
        <span>* 供货商不允许修改商品信息</span>
      </div>
    </el-form-item>

    <el-form-item label="自动更新" prop="retail_keep_sync" :class="formItemClass" v-if="isFromRetail">
      <el-switch
          v-model="form.retail_keep_sync"
          :disabled="!canEditRetail"
          inactive-text=""
          :active-value="1"
          :inactive-value="0"
          style="display: inline-block;">
      </el-switch>
      <div class="el-form-item__tip" style="margin-top: -10px">
        <span>* 保持&nbsp;"名称/描述/使用说明"&nbsp;与供货商一致
          <a class="icon-btn" title="查看详情">
            <i class="el-icon-info"
               @click="$msgbox({
               message:'开启后, 供货商修改 <b>名称/描述/使用说明</b> 时，<br>您的商品信息会自动更新 <br> <br>注意，开启后您修改的信息可能被覆盖',
               title:'说明',
               dangerouslyUseHTMLString: true})"
            ></i></a>
        </span>
      </div>
      <div class="el-form-item__tip"
           v-if="!canEditRetail">
        <span>* 供货商不允许修改商品信息</span>
      </div>
    </el-form-item>

    <el-form-item v-if="isFromRetail" label="进货价(元)" prop="cost" :class="formItemClass">
      <el-input type="text" placeholder="请输入商品成本价" :value="form.cost" disabled/>
      <div class="el-form-item__tip">
        <span>* 商品进货价格</span>
      </div>
    </el-form-item>

    <el-form-item label="售价(元)" prop="price" :class="formItemClass">
      <el-input type="text" placeholder="请输入商品售价"
                v-model="form.price"
                @change="v => form.price = +v||0"/>
      <div class="el-form-item__tip" v-if="isFromRetail">
        <span>* 当供货商修改价格时，您的售价会根据加价幅度同步修改
          <a class="icon-btn" title="查看详情"><i class="el-icon-info" @click="showRetailPriceTip"></i></a>
        </span>
      </div>
    </el-form-item>

    <el-form-item label="商品描述" prop="description" :class="formItemClass">
      <quill-editor ref="editor-description" :user-info="userInfo_" key="editor-description"
                    v-model="form.description"
                    :disabled="!canEditRetail"
                    placeholder="本图文说明将显示在商品购买页面"/>
      <div class="el-form-item__tip"
           v-if="!canEditRetail">
        <span>* 供货商不允许修改商品信息</span>
      </div>
    </el-form-item>

    <el-form-item label="使用说明" prop="instructions" :class="formItemClass">
      <quill-editor ref="editor-instructions" :user-info="userInfo_" key="editor-instructions"
                    v-model="form.instructions"
                    :disabled="!canEditRetail"
                    placeholder="本图文说明将显示在订单查询网站且购买成功的订单中，一般设置详细使用说明，售后QQ群，或者下载地址类"/>
      <div class="el-form-item__tip"
           v-if="!canEditRetail">
        <span>* 供货商不允许修改商品信息</span>
      </div>
    </el-form-item>

    <el-form-item label="状态" prop="enabled" :class="formItemClass">
      <el-radio-group v-model="form.enabled" size="small">
        <el-radio-button :label="1">上架</el-radio-button>
        <el-radio-button :label="0" class="el-radio-gray">下架</el-radio-button>
      </el-radio-group>
    </el-form-item>

    <div :class="formItemClass" class="more-setting-item">
      <el-checkbox v-model="showAdvancedInfo">勾选打开高级选项</el-checkbox>
    </div>

    <div v-show="showAdvancedInfo">

      <el-form-item v-if="!isFromRetail" label="成本价(元)" prop="cost" :class="formItemClass">
        <el-input type="text" placeholder="请输入商品成本价"
                  v-model="form.cost"
                  @change="v => form.cost = +v||0"/>
        <div class="el-form-item__tip">
          <span>* 成本价用于商户后台计算利润，默认0元</span>
        </div>
      </el-form-item>

      <el-form-item label="批发价" prop="price_whole" class="price-whole" :class="formItemClass">
        <div :style="{'line-height':isMobile?'18px':'inherit'}" class="extra-options-container">
          <el-button class="add-btn" size="mini" icon="el-icon-plus"
                     @click="()=>{price_whole.push([1,form.price]);checkFormPriceWhole()}">添加
          </el-button>
          <div class="price-body">
            <div v-for="(item,index) in price_whole" :key="'product_price_whole_'+index">
              <label class="item-label">件数
                <input class="item-input" type="number" min="1" max="10000" v-model.number="item[0]"
                       @change="checkFormPriceWhole"/>
              </label>
              <label class="item-label" style="margin-left: 8px">价格
                <input class="item-input" type="text" min="0.01" max="10000000" v-model.number="item[1]"
                       @change="checkFormPriceWhole"/>
              </label>
              <a class="del-btn" @click="()=>{price_whole.splice(index,1);checkFormPriceWhole()}">删除</a>
            </div>
          </div>
        </div>
        <div class="el-form-item__tip" v-if="form.retail_open">
          <span>* 只对普通用户有效, 代理分销时不生效</span>
        </div>
      </el-form-item>

      <el-form-item label="代理分销" prop="retail_open" :class="formItemClass" v-if="canRetail">
        <el-radio-group v-model="form.retail_open" size="small">
          <el-radio-button :label="1">开启</el-radio-button>
          <el-radio-button :label="0" class="el-radio-gray">关闭</el-radio-button>
        </el-radio-group>
        <transition name="el-zoom-in-center">
          <div v-show="form.retail_open" :class="formItemClass"
               class="extra-options-container"
               style="margin-top: 8px">

            <div>
              <el-switch
                  class="retail-allow-edit"
                  v-model="retail_options.can_edit"
                  inactive-text="下级代理可以修改商品信息"
                  :active-value="1"
                  :inactive-value="0"
                  style="display: inline-block;">
              </el-switch>
            </div>

            <div style="margin-top: 8px">
              <span class="el-form-item__label2" style="display: block">代理进货价</span>
              <div>
                <el-input ref="retail_price"
                          size="small" type="text" placeholder="请输入代理进货价"
                          v-model="retail_options.price"
                          @change="v => {retail_options.price= +v||0; checkRetailPrice()}"/>
                <div v-if="retail_price_check.message" class="el-form-item__tip"
                     :style="{color: retail_price_check.color}">
                  <span>{{ retail_price_check.message }}</span>
                </div>

                <div class="el-form-item__tip" v-if="isFromRetail">
                  <span>* 当供货商修改进货价时，代理进货价也会根据加价幅度同步修改</span>
                </div>

              </div>
            </div>

            <div style="margin-top: 16px">
              <span class="el-form-item__label2" style="display: block">代理零售价限制(元)</span>
              <div>
                <el-input ref="retail_min_price"
                          size="mini" placeholder="代理最低零售价"
                          :style="{'display':isMobile?'block':'inline-block', 'max-width':isMobile?'auto':'180px', 'margin-right': isMobile? '0': '8px'}"
                          type="text" v-model="retail_options.min_price"
                          @change="checkRetailPriceLimit"
                />
                <el-input
                    size="mini" placeholder="代理最高零售价"
                    :style="{'display':isMobile?'block':'inline-block', 'max-width':isMobile?'auto':'180px'}"
                    type="text" v-model="retail_options.max_price"
                    @change="checkRetailPriceLimit"
                />
                <div v-if="retail_priceLimit_check.message" class="el-form-item__tip"
                     :style="{color: retail_priceLimit_check.color}">
                  <span>{{ retail_priceLimit_check.message }}</span>
                </div>
              </div>

            </div>

            <div style="margin-top: 16px">
              <span class="el-form-item__label2" style="display: block">对接密钥</span>
              <div>
                <el-input ref="retail_code"
                          :style="{'max-width':isMobile?'auto':'372px'}"
                          class="input-retail-code"
                          @change="checkRetailCodeAvailable"
                          :minlength="6" :maxlength="32"
                          :disabled="retail_code_check.loading"
                          v-model="form.retail_code" placeholder="请输入6-32位字符"
                          size="mini">
                  <el-button slot="append" size="mini" type="text" :disabled="retail_code_check.loading"
                             @click="handleRandomRetailCode">随机
                  </el-button>
                </el-input>
                <div v-if="retail_code_check.message" class="el-form-item__tip"
                     :style="{color: retail_code_check.color}">
                  <span>{{ retail_code_check.message }}</span>
                </div>
                <div v-else class="el-form-item__tip">
                  <span>* 凭借本代码可以代理分销当前商品</span>
                </div>
              </div>
            </div>
          </div>
        </transition>
      </el-form-item>

      <el-form-item label="发货模式" prop="delivery" :class="formItemClass" v-if="PRODUCT_MANUAL">
        <el-radio-group v-model="form.delivery">
          <el-radio :label="DELIVERY.AUTO">自动发货</el-radio>
          <el-radio :label="DELIVERY.MANUAL">手动充值</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="买家信息" prop="fields" class="price-whole" :class="formItemClass" v-if="!isFromRetail">
        <el-select v-model="fields.type" size="small">
          <el-option label="可以输入任意字符" value="any"/>
          <el-option label="只能输入邮箱" value="email"/>
          <el-option label="只能输入手机号" value="mobile"/>
          <el-option label="只能输入QQ号" value="qq"/>
        </el-select>

        <div>
          <el-checkbox v-model="fields.need_ext" v-if="PRODUCT_MANUAL">需要填写收货信息</el-checkbox>
        </div>
        <transition name="el-zoom-in-center">
          <div class="price-body" v-if="fields.need_ext"
               :style="{'line-height':isMobile?'18px':'inherit', 'margin': isMobile ? '8px 0 4px' : '0'}">
            <el-button class="add-btn" size="mini" icon="el-icon-plus"
                       @click="()=>{fields.ext.push('名称');checkFieldsExt();}">添加
            </el-button>

            <div v-for="(name,index) in fields.ext" :key="'product_field_'+index">
              <label class="item-label">字段名
                <input class="item-input" style="width: 50%" type="text" v-model="fields.ext[index]"
                       @change="checkFieldsExt"/>
              </label>
              <a class="del-btn" @click="()=>{fields.ext.splice(index,1);checkFieldsExt();}">删除</a>
            </div>
          </div>
        </transition>

        <div v-if="fields_check.message" class="el-form-item__tip"
             :style="{color: fields_check.color}">
          <span>{{ fields_check.message }}</span>
        </div>
      </el-form-item>

      <el-form-item label="商品排序" prop="sort" :class="formItemClass">
        <el-input-number controls-position="right" :min="-10000000" :max="10000000" v-model="form.sort" size="small"/>
        <div class="el-form-item__tip">
          <span>* 数字越小越靠前</span>
        </div>
      </el-form-item>

      <el-form-item label="优惠券" prop="support_coupon" :class="formItemClass">
        <el-checkbox v-model="form.support_coupon">支持</el-checkbox>
      </el-form-item>

      <el-form-item label="购买限制" prop="buy_min" :class="formItemClass">
        <el-tooltip effect="dark" content="最小购买量" placement="top-start">
          <el-input-number controls-position="right" :min="1" :max="10000" v-model="form.buy_min" size="small"/>
        </el-tooltip>
        <el-tooltip effect="dark" content="最大购买量" placement="top-start">
          <el-input-number controls-position="right" :min="1" :max="10000" v-model="form.buy_max" size="small"/>
        </el-tooltip>
        <div class="el-form-item__tip" v-if="form.retail_open">
          <span>* 只对普通用户有效, 代理分销时不受限制</span>
        </div>
      </el-form-item>

      <el-form-item label="购买密码" prop="password" class="buy-password" :class="formItemClass">
        <el-tooltip effect="dark" content="设置商品购买密码，开启后只有输入密码才能购买" placement="top-start">
          <el-input type="text" placeholder="购买密码(可选)" v-model="form.password">
            <el-checkbox slot="prepend" v-model="form.password_open">开启</el-checkbox>
          </el-input>
        </el-tooltip>
        <div class="el-form-item__tip" v-if="form.retail_open">
        <span>* 只对普通用户有效, 代理分销时不受限制
         <a class="icon-btn" title="查看详情">
           <i class="el-icon-info" @click="showRetailPasswordTip"></i>
         </a>
        </span>
        </div>
      </el-form-item>

      

      <el-form-item label="库存预警" prop="count_warn" :class="formItemClass" v-if="!isFromRetail">
        <el-input-number controls-position="right" :min="0" :max="10000000" v-model="form.count_warn" size="small"/>
        <div class="el-form-item__tip">
          <span>* 到达预警值后会发送通知邮件，填0不预警</span>
        </div>
      </el-form-item>

      <el-form-item label="库存显示" prop="inventory" :class="formItemClass">
        <el-radio-group v-model="form.inventory" size="small">
          <el-radio-button :label="INVENTORY_TYPE.RANGE">大致范围</el-radio-button>
          <el-radio-button :label="INVENTORY_TYPE.REAL">实际库存</el-radio-button>
          <el-radio-button :label="INVENTORY_TYPE.AUTO" class="el-radio-gray">跟随店铺</el-radio-button>
        </el-radio-group>
      </el-form-item>

    </div>

    <el-form-item style="margin-top: 36px">
      <el-button :style="{float: isMobile ? 'right' : ''}" type="primary" @click="handleSubmit" :loading="loading">
        {{isAdd?'添加':'编辑'}}商品
      </el-button>
    </el-form-item>
  </el-form>

</div>
</template>
<script>
import NProgress from 'nprogress';
import { INVENTORY_TYPE } from '~/api/user';
import { edit as product_edit, check_retail_code, DELIVERY } from '~/api/product';
import { getList as getCategoryList } from '~/api/category';
import QuillEditor from '~/components/QuillEditor';
import { randomString, renderDelta } from '~/utils/helper';
import { moneyFilter } from '~/utils/filters';

// 是否开启手动发货功能
const PRODUCT_MANUAL = window.config.functions && window.config.functions.includes('product_manual');
export default {
  components: {
    QuillEditor
  },
  props: {
    info: {},
    inBox: {
      default: false
    },
    userInfo: {},
    _categories: {}
  },
  data() {
    return {
      loading: false,
      isMobile: this.$store.state.app.isMobile,
      PRODUCT_MANUAL,
      userInfo_: null,
      upload_url: window.API_URL + 'product/upload',
      INVENTORY_TYPE,
      DELIVERY,
      showAdvancedInfo: false,
      form: {
        id: null,
        user_id: null,
        category_id: null,
        name: '',
        description: '',
        // 仅用来校验表格, 不使用
        instructions: '',
        // 仅用来校验表格, 不使用
        fields: '',
        delivery: DELIVERY.AUTO,
        sort: 1000,
        buy_min: 1,
        buy_max: 1000,
        count_warn: '',
        inventory: INVENTORY_TYPE.AUTO,
        support_coupon: false,
        password: '',
        password_open: false,
        cost: 0,
        price: 0,
        price_whole: '',
        enabled: 1,
        retail_open: 0,
        retail_code: '',
        retail_from_id: null,
        retail_keep_sync: 1,
        webhook: ''
      },
      fields: {
        type: 'any',
        need_ext: false,
        ext: []
      },
      price_whole: [],
      categories: [],
      /** 当前商品的分销选项 */
      retail_options: {
        price: '',
        can_edit: 1,
        min_price: 0,
        max_price: 99999
      },
      formRules: {
        category_id: [{
          required: true,
          message: '请选择商品分类',
          trigger: 'change'
        }],
        name: [{
          required: true,
          message: '请输入商品名称',
          trigger: 'change'
        }],
        cost: [{
          validator: (rule, value, callback) => {
            const tmp = Math.round(value * 100);
            if (tmp < 0) {
              callback(new Error('成本价不能低于 0'));
              return;
            }
            if (tmp > 1000000000) {
              callback(new Error('成本价不能超过 10000000'));
              return;
            }
            callback();
          },
          trigger: 'change'
        }],
        price: [{
          required: true,
          validator: (rule, value, callback) => {
            const tmp = Math.round(value * 100);
            if (tmp < Math.round(this.form.cost * 100)) {
              callback(new Error('商品售价不能低于成本价'));
            }
            if (tmp < 1) {
              callback(new Error('商品售价不能低于 0.01'));
              return;
            }
            if (tmp > 1000000000) {
              callback(new Error('商品售价不能超过 10000000'));
              return;
            }
            if (this.isFromRetail && this.retail_from) {
              if (tmp < this.retail_from.retail_options.min_price || tmp > this.retail_from.retail_options.max_price) {
                callback(new Error(`* 供货商限定价格为: ${moneyFilter(this.retail_from.retail_options.min_price)} - ${moneyFilter(this.retail_from.retail_options.max_price)}`));
                return;
              }
            }
            callback();
          },
          trigger: 'change'
        }],
        price_whole: [{
          validator: (rule, value, callback) => {
            for (let i = 0; i < this.price_whole.length; i++) {
              if (this.price_whole[i][1] < 0.01) {
                callback(new Error('批发价格不能低于 0.01'));
                return;
              }
              if (this.price_whole[i][1] > 10000000) {
                callback(new Error('批发价格不能超过 10000000'));
                return;
              }
              const tmp = Math.round(this.price_whole[i][1] * 100);
              if (this.isFromRetail && this.retail_from) {
                if (tmp < this.retail_from.retail_options.price) {
                  callback(new Error('批发价格不能低于成本价'));
                  return;
                }
                if (tmp < this.retail_from.retail_options.min_price || tmp > this.retail_from.retail_options.max_price) {
                  callback(new Error(`* 供货商限定价格为: ${moneyFilter(this.retail_from.retail_options.min_price)} - ${moneyFilter(this.retail_from.retail_options.max_price)}`));
                  return;
                }
              }
            }
            callback();
          },
          trigger: 'change'
        }],
        enabled: [{
          required: true,
          message: '请选择状态',
          trigger: 'change'
        }]
      },
      showRetailFromInfo: false,
      retail_from: null,
      fields_check: {
        message: '',
        color: ''
      },
      retail_price_check: {
        message: '',
        color: ''
      },
      retail_priceLimit_check: {
        message: '',
        color: ''
      },
      retail_code_check: {
        loading: false,
        message: '',
        color: ''
      }
    };
  },
  computed: {
    isAdd() {
      return !this.info;
    },
    formItemClass() {
      return this.isMobile ? 'product-form-item' : '';
    },
    isFromRetail() {
      return false;
    },
    canEditRetail() {
      return !(this.isFromRetail && this.retail_from !== null && this.retail_from.retail_options.can_edit === 0);
    },
    canRetail() {
      return false;
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
    },
    _categories() {
      this.categories = this._categories;
    }
  },
  beforeMount() {
    
    if (this.userInfo) {
      this.userInfo_ = this.userInfo;
    } else {
      this.userInfo_ = this.$store.state.user.userInfo;
    }
    if (this._categories && this._categories.length && this._categories[0].id > 0) {
      this.categories = this._categories;
    } else {
      this.getCategories();
    }
    if (!this.isAdd) {
      for (const key in this.form) {
        if (Object.prototype.hasOwnProperty.call(this.form, key)) this.$set(this.form, key, this.info[key]);
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
      if (this.form.fields && this.form.fields[0] === '{') {
        Object.assign(this.fields, JSON.parse(this.form.fields));
      }
      this.retail_from = this.info.retail_from;
      if (this.retail_from) {
        if (this.retail_from.retail_options.can_edit === 0) {
          this.form.retail_keep_sync = 1;
        }
      }
      if (this.info.retail_options) {
        Object.assign(this.retail_options, this.info.retail_options);
        this.retail_options.price = (this.retail_options.price / 100).toFixed(2) * 1.0;
        this.retail_options.min_price = (this.retail_options.min_price / 100).toFixed(2) * 1.0;
        this.retail_options.max_price = (this.retail_options.max_price / 100).toFixed(2) * 1.0;
      }
    }
    if (this.userInfo) {
      this.form.user_id = this.userInfo.id;
    }
    if (this.$route.query.retail_code) {
      this.addRetailProduct(this.$route.query.retail_code);
    }
  },
  methods: {
    moneyFilter,
    renderDelta,
    getCategories() {
      const where = {
        search: 'simple',
        val: 'list'
      };
      if (this.info) {
        where.user_id = this.info.user_id;
      } else if (this.userInfo) {
        where.user_id = this.userInfo.id;
      }
      getCategoryList(where).then(ret => {
        this.categories = ret.data;
        
        
        
        //   }).then(() => {
        
        
        //     } else {
        
        
        
        
        
        
      });
    },
    async addRetailProduct(retail_code) {
      if (!retail_code) {
        try {
          retail_code = (await this.$prompt('请输入供货商提供的对接密钥', '请输入对接密钥', {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            inputPattern: /.{6,32}/,
            inputErrorMessage: '对接密钥为 6 到 32 位',
            closeOnClickModal: false
          })).value;
        } catch (e) {
          return;
        }
      }
      this.loading = true;
      check_retail_code(retail_code).then(res => {
        if (res.message === 'product') {
          const tmp = res.data;
          this.form.from_code = retail_code;
          this.form.name = tmp.name;
          this.form.description = tmp.description;
          this.form.instructions = tmp.instructions;
          this.$refs['editor-description'].setValue(tmp.description);
          this.$refs['editor-instructions'].setValue(tmp.instructions);
          this.form.cost = (tmp.retail_options.price / 100).toFixed(2) * 1.0;
          this.form.price = (tmp.retail_options.min_price / 100).toFixed(2) * 1.0;
          this.retail_from = tmp;
        } else if (res.message === 'user') {
          this.$emit('changeTab', {
            to: 'product_retail',
            retail_code
          });
        } else {
          this.$msgbox({
            message: res.message,
            title: '错误',
            type: 'error'
          });
        }
      }).finally(() => {
        this.loading = false;
      });
    },
    showRetailPriceTip() {
      this.$msgbox({
        message: "\n        当供货商修改商品进货价格时，<br>您设置的价格会根据供货商的修改幅度同步更改。<br><br>\n\n        举例：<br>\n        供货商设置进货价为 10 元，<br>您设置的价格为 15 元；<br>\n        当供货商将进货价提高为 11 元时，<br>您的价格将自动修改为 16 元。\n        ",
        title: '供货商修改价格说明',
        dangerouslyUseHTMLString: true
      });
    },
    showRetailPasswordTip() {
      this.$msgbox({
        message: "\n此选项仅在用户直接购买时有效，<br>代理进货时不受限制。\n",
        title: '购买密码说明',
        dangerouslyUseHTMLString: true
      });
    },
    checkFieldsExt() {
      this.fields_check.message = '';
      if (!this.fields.need_ext) {
        return true;
      }
      if (!this.fields.ext.length) {
        this.fields_check.message = '请至少添加一个字段';
        this.fields_check.color = '#FF4949';
        return false;
      }
      for (let i = 0; i < this.fields.ext.length; i++) {
        if (!this.fields.ext[i].length) {
          this.fields_check.message = `第${i + 1}个字段的名称不能为空`;
          this.fields_check.color = '#F7BA2A';
          return false;
        }
      }
    },
    // 检查代理进货价
    checkRetailPrice() {
      if (!this.form.retail_open) {
        this.retail_price_check.message = '';
        this.retail_price_check.color = '';
        return true;
      }
      if (this.retail_options.price === '') {
        this.retail_price_check.message = '请输入 代理进货价';
        this.retail_price_check.color = '#F7BA2A';
        return false;
      }
      if (+this.retail_options.price < +this.form.cost) {
        this.retail_price_check.message = '代理进货价不能低于商品成本价';
        this.retail_price_check.color = '#FF4949';
        return false;
      }
      if (+this.retail_options.price > 10000000) {
        this.retail_price_check.message = '价格不能超过 10,000,000';
        this.retail_price_check.color = '#FF4949';
        return false;
      }
      this.retail_price_check.message = '';
      this.retail_price_check.color = '';
      return true;
    },
    // 检查代理售价范围
    checkRetailPriceLimit() {
      if (!this.form.retail_open) {
        this.retail_priceLimit_check.message = '';
        this.retail_priceLimit_check.color = '';
        return true;
      }
      if (this.retail_options.min_price === '') {
        this.retail_priceLimit_check.message = '请输入 代理最低零售价';
        this.retail_priceLimit_check.color = '#F7BA2A';
        return false;
      }
      if (this.retail_options.max_price === '') {
        this.retail_priceLimit_check.message = '请输入 代理最高零售价';
        this.retail_priceLimit_check.color = '#F7BA2A';
        return false;
      }
      
      
      
      
      

      if (+this.retail_options.min_price > +this.retail_options.max_price) {
        this.retail_priceLimit_check.message = '代理最高零售价 需要大于 代理最低零售价';
        this.retail_priceLimit_check.color = '#FF4949';
        return false;
      }
      if (+this.retail_options.min_price > 10000000 || +this.retail_options.max_price > 10000000) {
        this.retail_priceLimit_check.message = '价格不能超过 10,000,000';
        this.retail_priceLimit_check.color = '#FF4949';
        return false;
      }
      this.retail_priceLimit_check.message = '';
      this.retail_priceLimit_check.color = '';
      return true;
    },
    // 在批发价修改, 增加新项目时, 触发批发价检查
    checkFormPriceWhole() {
      return this.$refs['form'].validateField('price_whole');
    },
    async checkRetailCodeAvailable(full_check = true) {
      if (!this.form.retail_code || this.form.retail_code.length < 6) {
        this.retail_code_check.message = '对接密钥为 6 到 32 位';
        this.retail_code_check.color = '#FF4949';
        return false;
      }
      if (this.form.retail_code.length > 32) {
        this.retail_code_check.message = '对接密钥为 6 到 32 位';
        this.retail_code_check.color = '#FF4949';
        return false;
      }
      if (!full_check) {
        return this.retail_code_check.message === '';
      }
      this.retail_code_check.message = '对接密钥检查中...请稍后';
      this.retail_code_check.color = '#F7BA2A';
      this.retail_code_check.loading = true;
      try {
        const res = await check_retail_code(this.form.retail_code);
        if (res.data && (res.data.id !== this.form.id || res.message !== 'product')) {
          this.retail_code_check.message = '对接密钥已被使用，请重新输入';
          this.retail_code_check.color = '#FF4949';
          this.$notify({
            title: '提示',
            message: this.retail_code_check.message,
            type: 'error'
          });
        } else {
          this.retail_code_check.message = '';
          this.retail_code_check.color = '';
          this.$notify({
            title: '提示',
            message: '当前对接密钥可用',
            type: 'success',
            duration: 500
          });
          return true;
        }
      } catch (e) {
        this.retail_code_check.message = '对接密钥检查失败，请重新输入';
        console.error(e);
      } finally {
        this.retail_code_check.loading = false;
      }
      return false;
    },
    handleRandomRetailCode() {
      this.form.retail_code = randomString(8).toLocaleUpperCase();
      this.checkRetailCodeAvailable();
    },
    async handleSubmit() {
      // 后台会判断已经删除 or 关闭分销 是否可以编辑
      this.form.description = this.$refs['editor-description'].getValue();
      this.form.instructions = this.$refs['editor-instructions'].getValue();
      this.$refs['form'].validate(async valid => {
        if (!valid) {
          this.$msgbox({
            message: '信息填写不完整, 请检查',
            title: '提示',
            type: 'error'
          });
          return false;
        }
        if (this.form.retail_open) {
          if (!(await this.checkRetailPrice())) {
            return this.$msgbox({
              message: this.retail_price_check.message,
              title: '提示',
              type: 'error'
            }).then(() => {
              this.$refs.retail_price.focus();
            });
          }
          if (!(await this.checkRetailPriceLimit())) {
            return this.$msgbox({
              message: this.retail_priceLimit_check.message,
              title: '提示',
              type: 'error'
            }).then(() => {
              this.$refs.retail_min_price.focus();
            });
          }
          if (!(await this.checkRetailCodeAvailable(false))) {
            return this.$msgbox({
              message: this.retail_code_check.message,
              title: '提示',
              type: 'error'
            }).then(() => {
              this.$refs.retail_code.focus();
            });
          }
        }
        this.loading = true;
        const isAddCategory = typeof this.form.category_id === 'string';
        if (isAddCategory) {
          this.form.category_id = '+' + this.form.category_id;
          this.form.category = {
            name: this.form.category_id
          };
        }
        const tmp = Object.assign({}, this.form);
        tmp.fields = JSON.stringify(this.fields);
        tmp.price_whole = JSON.stringify(this.price_whole.map(e => [e[0], parseInt(e[1] * 100)]));
        if (this.isAdd) {
          delete tmp.id;
        }
        if (tmp.retail_open) {
          tmp.retail_options = this.retail_options;
        } else {
          delete tmp.retail_code;
        }
        product_edit(tmp).then(res => {
          this.loading = false;
          if (res.data.category) {
            this.categories.push(res.data.category);
            this.form.category_id = res.data.category.id;
          }
          if (this.isAdd) {
            this.$msgbox({
              title: '提示',
              message: '添加商品成功',
              showCancelButton: true,
              dangerouslyUseHTMLString: true,
              confirmButtonText: '查看商品',
              cancelButtonText: '继续添加'
            }).then(() => {
              this.$router.push('/admin/product/list');
            });
          } else {
            this.$notify({
              title: '提示',
              message: '编辑商品成功',
              type: 'success',
              duration: 3000
            });

            // 编辑商品
            this.$emit('submit', true);
          }
        }).finally(() => {
          this.loading = false;
        });
      });
    }
  }
};
</script>

<style lang="scss">
  @import "../edit";

  .retail-root-info {
    font-size: 14px;

    p {
      margin: 4px 0;
    }
  }

  .extra-options-container {
    border: 1px solid #eee;
    margin-bottom: 4px;
    padding: 8px;
  }

  .el-switch.retail-allow-edit {
    .el-switch__label span {
      font-weight: normal !important;
    }
  }

  .more-setting-item {
    margin: 24px 0;
    padding: 8px 24px;
    background-color: #f5f8ff;

    .el-checkbox {
      color: #a2a9ba;
    }
  }
</style>
