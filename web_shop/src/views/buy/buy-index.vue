<template>
<div class="content" v-if="shop">

  <buy-header :shop="shop"/>

  <!-- 三种模式, products-button / products-dropdown / products-list -->
  <!-- 这里需要注意, list列表式是平铺模式, 要展示所有商品, 当选中商品时(slot-info展现) 列表会自动隐藏, 展示商品详情 -->
  <component :is="'products-'+showType" v-if="singleMode!=='flex' || !payConfirmShow"
             :categories="categories" :category.sync="category" :product.sync="product"
             :categoryErr="categoryErr" :productErr="productErr"
  >
    <div v-if="product" slot="info">

      <!-- 旧模式, 上下排列 -->
      <div v-if="singleMode==='flow'" class="product-flow">
        <div>
          <div v-if="(showType === 'list' || shopType === 'product') && categories && categories.length">
            <input type="hidden" name="category_id" :value="category.id" title="category_id"/>
            <span class="title">商品分类：</span>
            <span>{{category.name}}</span>
          </div>
          <div v-if="(showType === 'list' || shopType === 'product') && product">
            <input type="hidden" name="product_id" :value="product.id" title="product_id"/>
            <span class="title">商品名称：</span>
            <span>{{product.name}}</span>
          </div>
          <div class="info-item">
            <p><span class="title">单价：</span>
              <span class="val">{{product.price | moneyFilter | moneyFormatterFilter}}</span>
            </p>
            <p><span class="title">库存：</span>
              <span class="val">{{product.count2}}</span>
            </p>
          </div>
          <div class="info-item">
            <p v-if="canPriceWhole">
              <span class="title">批发价：</span>
              <span class="val">
                    <a style="cursor: pointer"
                       @mouseover="showDiscountTip = true" @mouseleave="showDiscountTip = false"
                       @click="showDiscount(product)">点击查看</a>
                    <div class="discount-tip" v-html="renderDiscount" v-if="showDiscountTip"></div>
              </span>
            </p>
            <p v-if="product.support_coupon">
              <span class="title">优惠券：</span>
              <span class="val">{{product.support_coupon ? '支持' : '不可用'}}</span>
            </p>
          </div>
          <p class="description" v-html="renderDelta(product.description)"></p>
        </div>

        <mu-text-field name="count" type="number" :errorText="countErr" @input="onCountInput"
                       labelFloat label="购买数目" class="buy-count" v-model="buyCount" style="width: 64px"/>
        <div class="price-item equal-with-mu" style="vertical-align: top">
          <span class="money">{{renderPrice.price | moneyFilter | moneyFormatterFilter}}</span>
          <span class="money-discount" v-if="renderPrice.discount">（已优惠{{renderPrice.discount | moneyFilter | moneyFormatterFilter}}）</span>
          <span class="money-fee"
                v-if="renderPrice.fee">（手续费{{renderPrice.fee | moneyFilter | moneyFormatterFilter}}）</span>
        </div>

        <mu-text-field v-if="fields.type === 'email'" name="email" :errorText="contactErr"
                       v-model="contact" @input="onContactInput"
                       type="text" label="联系邮箱" placeholder="请输入邮箱，用于查询订单" style="width: 100%"/>
        <mu-text-field v-else-if="fields.type === 'mobile'" name="mobile" :errorText="contactErr"
                       v-model="contact" @input="onContactInput"
                       type="text" label="联系手机号" placeholder="请输入手机号，用于查询订单" style="width: 100%"/>
        <mu-text-field v-else-if="fields.type === 'qq'" name="qq" :errorText="contactErr"
                       v-model="contact" @input="onContactInput"
                       type="text" label="联系QQ" placeholder="请输入QQ号码，用于查询订单" style="width: 100%"/>
        <mu-text-field v-else name="contact" :errorText="contactErr"
                       v-model="contact" @input="onContactInput"
                       type="text" label="联系方式" placeholder="可以输入QQ、邮箱、手机号等等，用于查询订单" style="width: 100%"/>

        <mu-text-field v-if="ORDER_QUERY_PASSWORD" name="query_password" :errorText="queryPassErr"
                       v-model="query_password" @input="onQueryPasswordInput"
                       type="text" label="查询密码" placeholder="请输入订单查询密码" style="width: 100%"/>

        <div v-if="fields.need_ext">
          <div v-for="(_, index) in fields.ext" :key="'custom_field_'+index">
            <mu-text-field v-model="fields.values[index]" :errorText="fields.errors[index]"
                           @input="onContactExtInput"
                           type="text" :label="fields.ext[index]" style="width: 100%"/>
          </div>
        </div>

        <mu-text-field name="coupon" autocomplete="off" v-if="product.support_coupon"
                       v-model="coupon"
                       @change="onCouponChange"
                       :errorText="couponErr"
                       type="text" label="优惠券" placeholder="请输入优惠券(没有可不填)" style="width: 100%"/>


        <div class="label mu-text-field-label"
             v-if="SMS_SEND_ORDER || (MAIL_SEND_ORDER && !MAIL_SEND_ORDER_USE_CONTACT)">附加服务
        </div>
        <div style="margin: 0 0 0 -2px" v-if="SMS_SEND_ORDER">
          <mu-checkbox v-model="sendSms" @change="onSendSmsChange"
                       :label="'短信通知订单结果' + (sms_send_order.sms_price ? ('（￥'+sms_send_order.sms_price/100 + '）') : '')"></mu-checkbox>
        </div>

        <div style="margin: 0 0 8px -2px" v-if="MAIL_SEND_ORDER && !MAIL_SEND_ORDER_USE_CONTACT">
          <mu-checkbox v-model="sendMail" @change="onSendMailChange"
                       label="邮件通知订单结果"></mu-checkbox>
        </div>

        <transition name="top-slide">
          <mu-text-field v-if="sendSms" name="mobile" :errorText="smsErr"
                         v-model="smsTo" @input="onSmsInput"
                         type="text" label="接收订单手机号" placeholder="请输入手机号，用于接收订单短信" style="width: 100%"/>
        </transition>

        <transition name="top-slide">
          <mu-text-field v-if="sendMail" name="email" :errorText="mailErr"
                         v-model="mailTo" @input="onMailInput"
                         type="text" label="接收订单邮箱" placeholder="请输入邮箱，用于接收订单邮件" style="width: 100%"/>
        </transition>


        <div class="pay-way-item">
          <div class="label mu-text-field-label">支付方式</div>
          <payway v-model="payway"/>
          <div class="pay-btn-container">
            <mu-button class="btn-pay" :disabled="submitting"
                       @click="handlePay">
              {{submitting ?'加载中':'立即购买'}}
            </mu-button>
          </div>
        </div>
      </div>

      <!-- 左右并排, 商城模式 -->
      <div v-if="singleMode==='flex'" class="product-flex">
        <div class="product-info">
          <div class="product-name">
            <h2>{{product.name}}</h2>
          </div>

          <div class="info-item price-container">
            <div class="info-title">商品单价：</div>
            <div class="info-val price">
              {{product.price | moneyFilter | moneyFormatterFilter}}
            </div>

            <div class="price-whole-container" v-if="canPriceWhole">
              <div class="info-title">批发优惠：</div>
              <div class="info-val">
                <a style="cursor: pointer"
                   @mouseover="showDiscountTip = true" @mouseleave="showDiscountTip = false"
                   @click="showDiscount(product)">点击查看</a>
                <div class="discount-tip" v-html="renderDiscount" v-if="showDiscountTip"></div>
              </div>
            </div>
          </div>

          <div class="info-item delivery-item">
            <div class="info-title">发货方式：</div>
            <div class="info-val info-delivery">
              <mu-chip v-if="product.delivery===0" color="#00b5ff" text-color="#fff">自动发货</mu-chip>
              <mu-chip v-else color="#dd9f00" text-color="#fff">手动充值</mu-chip>
            </div>
          </div>

          <div class="info-item input-item">
            <div class="input-fields" v-if="fields.type === 'email'">
              <mu-text-field name="email" :errorText="contactErr"
                             v-model="contact" @input="onContactInput"
                             type="text" label="联系邮箱" placeholder="请输入邮箱，用于查询订单"/>
            </div>
            <div class="input-fields" v-else-if="fields.type === 'mobile'">
              <mu-text-field name="mobile" :errorText="contactErr"
                             v-model="contact" @input="onContactInput"
                             type="text" label="联系手机号" placeholder="请输入手机号，用于查询订单"/>
            </div>
            <div class="input-fields" v-else-if="fields.type === 'qq'">
              <mu-text-field name="qq" :errorText="contactErr"
                             v-model="contact" @input="onContactInput"
                             type="text" label="联系QQ" placeholder="请输入QQ号码，用于查询订单"/>
            </div>
            <div class="input-fields" v-else>
              <mu-text-field name="contact" :errorText="contactErr"
                             v-model="contact" @input="onContactInput"
                             type="text" label="联系方式" placeholder="可以输入QQ、邮箱、手机号等等，用于查询订单"/>
            </div>

            <div class="input-fields" v-if="ORDER_QUERY_PASSWORD" >
              <mu-text-field name="query_password" :errorText="queryPassErr"
                             v-model="query_password" @input="onQueryPasswordInput"
                             type="text" label="查询密码" placeholder="请输入订单查询密码" style="width: 100%"/>
            </div>

            <div class="input-fields" v-if="fields.need_ext">
              <div v-for="(_, index) in fields.ext" :key="'custom_field_'+index">
                <mu-text-field v-model="fields.values[index]" :errorText="fields.errors[index]"
                               @input="onContactExtInput"
                               type="text" :label="fields.ext[index]"/>
              </div>
            </div>


            <div class="input-coupon" v-if="product.support_coupon">
              <mu-text-field name="coupon" autocomplete="off"
                             v-model="coupon"
                             @change="onCouponChange"
                             :errorText="couponErr"
                             type="text" label="优惠券" placeholder="请输入优惠券(没有可不填)"/>
            </div>

            <div class="input-count">
              <mu-text-field name="count" type="number" autocomplete="off" :errorText="countErr"
                             @input="onCountInput" placeholder="请输入购买数量"
                             label="购买数量" class="buy-count" v-model.number="buyCount"/>
              <span class="count-left">库存&nbsp;<span style="color: #e70602">{{product.count2}}</span></span>
            </div>


            <div style="margin: 4px 0 0 -2px" v-if="SMS_SEND_ORDER">
              <mu-checkbox v-model="sendSms" @change="onSendSmsChange"
                           :label="'短信通知订单结果（￥' + (sms_send_order.sms_price/100) + '）'"></mu-checkbox>
            </div>

            <div style="margin: 4px 0 0 -2px" v-if="MAIL_SEND_ORDER && !MAIL_SEND_ORDER_USE_CONTACT">
              <mu-checkbox v-model="sendMail" @change="onSendMailChange"
                           label="邮件通知订单结果（免费)"></mu-checkbox>
            </div>

            <transition name="top-slide">
              <div class="info-item delivery-item" v-if="sendSms">
                <mu-text-field name="mobile" :errorText="smsErr"
                               v-model="smsTo" @input="onSmsInput"
                               type="text" label="接收订单手机号" placeholder="请输入手机号，用于接收订单短信" style="width: 100%"/>
              </div>
            </transition>

            <transition name="top-slide">
              <div class="info-item delivery-item" v-if="sendMail">
                <mu-text-field name="email" :errorText="mailErr"
                               v-model="mailTo" @input="onMailInput"
                               type="text" label="接收订单邮箱" placeholder="请输入邮箱，用于接收订单邮件" style="width: 100%"/>
              </div>
            </transition>

          </div>

          <div class="info-item">
            <div class="info-title">商品总价：</div>
            <div class="info-val">
              <div class="money-total">
                <span class="money">{{renderPrice.price | moneyFilter | moneyFormatterFilter}}</span>
                <span class="money-discount" v-if="renderPrice.discount">（已优惠{{renderPrice.discount | moneyFilter | moneyFormatterFilter}}）</span>
                <span class="money-fee" v-if="renderPrice.fee">（手续费{{renderPrice.fee | moneyFilter | moneyFormatterFilter}}）</span>
              </div>
            </div>
          </div>

          <div class="pay-btn-container">
            <mu-button class="btn-pay" :disabled="submitting"
                       @click="handlePay">
              {{submitting ? '加载中':'立即购买'}}
            </mu-button>

            <mu-button v-if="showType === 'list'" class="btn-cancel" @click="product=null">返回</mu-button>
          </div>
        </div>

        <div class="product-gap"></div>

        <!-- 位于左侧, 但是手机端会自动下沉位于底部 -->
        <div class="product-description">
          <p class="description" v-html="renderDelta(product.description)"></p>
        </div>
      </div>

    </div>
  </component>

  <!-- 单商品模式为 flex, 左右并排  手机端时, 此框显示, 商品描述显示在下面 -->
  <mu-card v-if="singleMode==='flex' && product && !showType_List_ShowList && !payConfirmShow"
           class="my-card white-background description-mobile">
    <div class="mu-card-text">
      <mu-circular-progress v-if="product.progress" :size="40" style="width: 100%; text-align: center"/>
      <div v-else>
        <div class="description-title">
          <h3>商品说明</h3>
        </div>
        <p class="description" v-html="renderDelta(product.description)"></p>
      </div>
    </div>
  </mu-card>

  <!-- 左右并排, 商城模式  单独的付款确认页面 -->
  <mu-card v-if="singleMode==='flex' && product && payConfirmShow" class="my-card white-background pay-confirm-card">
    <mu-card-text>
      <a v-if="showType === 'list'" style="float: right; margin: -14px -14px 0 0;">
        <mu-button color="red" icon @click="payConfirmShow=false">
          <mu-icon value="close"></mu-icon>
        </mu-button>
      </a>
      <div class="confirm-title">
        <h3>购买确认</h3>
      </div>

      <p class="confirm-item">
        <span class="title">购买商品：</span>{{product.name}}</p>
      <p class="confirm-item">
        <span class="title">商品单价：</span>
        <span class="money">{{pricePer | moneyFilter | moneyFormatterFilter}}</span>
        <span class="money-fee" v-if="pricePer!==product.price">（批发优惠）</span>
      </p>
      <p class="confirm-item">
        <span class="title">购买件数：</span>{{buyCount}}</p>
      <p class="confirm-item" v-if="renderPrice.discount">
        <span class="title">优惠金额：</span>
        <span class="money">优惠券抵扣&nbsp;{{renderPrice.discount | moneyFilter | moneyFormatterFilter}}</span>
      </p>
      <p class="confirm-item">
        <span class="title">支付金额：</span>
        <span class="money">{{renderPrice.price | moneyFilter | moneyFormatterFilter}}</span>
        <span class="money-fee"
              v-if="renderPrice.fee">（手续费{{renderPrice.fee | moneyFilter | moneyFormatterFilter}}）</span>
      </p>

      <payway v-model="payway"/>

      <div class="confirm-btn-container">
        <mu-button class="btn-confirm" primary @click="confirmPay" :disabled="$captcha.loading">{{ $captcha.loading ? '加载中' : '确定支付' }}</mu-button>
        <mu-button class="btn-cancel" @click="payConfirmShow=false">返回</mu-button>
      </div>
    </mu-card-text>
  </mu-card>

  <!-- 普通模式  弹窗购买确认页面 -->
  <mu-dialog v-if="singleMode==='flow' && product && payway" :open="payConfirmShow"
             title="购买确认"
             @close="payConfirmShow=false">
    <p class="confirm-item">
      <span class="title">购买商品：</span>{{product.name}}</p>
    <p class="confirm-item">
      <span class="title">商品单价：</span>
      <span class="money">{{pricePer | moneyFilter | moneyFormatterFilter}}</span>
      <span class="money-fee" v-if="pricePer!==product.price">（批发优惠）</span>
    </p>
    <p class="confirm-item">
      <span class="title">购买件数：</span>{{buyCount}}</p>
    <p class="confirm-item" v-if="renderPrice.discount">
      <span class="title">优惠金额：</span>
      <span class="money">优惠券抵扣&nbsp;{{renderPrice.discount | moneyFilter | moneyFormatterFilter}}</span>
    </p>
    <p class="confirm-item">
      <span class="title">支付金额：</span>
      <span class="money">{{renderPrice.price | moneyFilter | moneyFormatterFilter}}</span>
      <span class="money-fee"
            v-if="renderPrice.fee">（手续费{{renderPrice.fee | moneyFilter | moneyFormatterFilter}}）</span>
    </p>
    <p class="confirm-item">
      <span class="title">支付方式：</span>{{payway.name}}</p>
    <mu-button flat slot="actions" @click="payConfirmShow=false">取消</mu-button>
    <mu-button flat slot="actions" primary @click="confirmPay" :disabled="$captcha.loading">{{ $captcha.loading ? '加载中' : '确定支付' }}</mu-button>
  </mu-dialog>

</div>
<div v-else style="text-align: center">
  当前店铺无商品
</div>
</template>

<script>
import { renderDelta, cookie, randomString, getParameterByName, param } from '~/utils/helper';
import { validateEmail, validatNumbers } from '~/utils/validate';
import Payway from "@/views/buy/payway";
import BuyHeader from "@/views/buy/buy-header";
import ProductsList from "@/views/buy/products-list";
import ProductsDropdown from "@/views/buy/products-dropdown";
import ProductsButton from "@/views/buy/products-button";
import ProductsNull from "@/views/buy/products-null";
function validateMobile(mobile) {
  return !isNaN(mobile) && mobile[0] === '1' && mobile.length === 11;
}
const DISCOUNT_TYPE_AMOUNT = 0;
const DISCOUNT_TYPE_PERCENT = 1;
const MAIL_SEND_ORDER = window.config.functions && window.config.functions.includes('mail_send_order');
const MAIL_SEND_ORDER_USE_CONTACT = window.config.functions && window.config.functions.includes('mail_send_order_use_contact');

// 是否开启短信功能
const SMS_SEND_ORDER = window.config.functions && window.config.functions.includes('sms_send_order');

// 是否开启查询密码
const ORDER_QUERY_PASSWORD = window.config.functions && window.config.functions.includes('order_query_password');
export default {
  components: {
    ProductsButton,
    ProductsDropdown,
    ProductsList,
    ProductsNull,
    BuyHeader,
    Payway
  },
  data() {
    let shopType = 'shop';
    let showType = window.config.theme.list_type || 'button';
    if (window.config.product && window.config.product.id > 0) {
      shopType = 'product';
      showType = 'null'; // 这时候没有list !!
    }
    const singleMode = window.config.theme.single_mode || 'flow';
    return {
      config: window.config,
      shopType,
      shop: window.config.shop,
      showType,
      singleMode,
      MAIL_SEND_ORDER,
      MAIL_SEND_ORDER_USE_CONTACT,
      SMS_SEND_ORDER,
      ORDER_QUERY_PASSWORD,
      sms_send_order: window.config.sms_send_order,
      // 配置信息
      submitting: false,
      fields: {
        type: 'any',
        need_ext: false,
        ext: [],
        values: [],
        errors: []
      },
      category: null,
      categories: [],
      product: null,
      showDiscountTip: false,
      buyCount: '1',
      sendSms: false,
      smsTo: '',
      sendMail: false,
      mailTo: '',
      coupon: '',
      contact: '',
      query_password: '',
      countErr: '',
      couponErr: '',
      contactErr: '',
      queryPassErr: '',
      smsErr: '',
      mailErr: '',
      couponInfo: null,
      payway: null,
      payConfirmShow: false
    };
  },
  computed: {
    categoryErr() {
      return !this.category ? '请选择商品分类' : '';
    },
    productErr() {
      return !this.product ? '请选择商品' : '';
    },
    pricePer() {
      const buyCount = parseInt(this.buyCount);
      let price = this.product.price;
      if (this.canPriceWhole) {
        const price_whole = this.product.price_whole;
        for (let i = price_whole.length - 1; i >= 0; i--) {
          if (buyCount >= parseInt(price_whole[i][0])) {
            price = parseInt(price_whole[i][1]);
            break;
          }
        }
      }
      return price;
    },
    priceAll() {
      // 优惠之前
      const buyCount = parseInt(this.buyCount) || 0;
      return this.pricePer * buyCount;
    },
    renderPrice() {
      let price_pay = this.priceAll;
      let discount = 0;
      if (this.couponInfo) {
        if (this.couponInfo.discount_type === DISCOUNT_TYPE_AMOUNT) {
          if (price_pay >= this.couponInfo.discount_val) {
            discount = this.couponInfo.discount_val;
          }
        } else if (this.couponInfo.discount_type === DISCOUNT_TYPE_PERCENT) {
          discount = Math.round(price_pay * this.couponInfo.discount_val / 100);
        }
        price_pay -= discount;
      }

      // 买家承担手续费
      let fee = 0;
      if (+this.shop.fee_type === 1 && this.payway) {
        // 四舍五入
        fee = Math.round(price_pay * this.payway.fee / (1 - this.payway.fee));
        if (fee > 0) {
          price_pay += fee;
        }
      }
      if (this.sendSms) {
        price_pay += this.sms_send_order.sms_price;
      }
      return {
        price: price_pay,
        fee,
        discount
      };
    },
    renderDiscount() {
      if (!this.canPriceWhole) {
        return '无';
      }
      const price_whole = this.product.price_whole;
      let msg = '';
      price_whole.forEach(e => {
        msg += `满${e[0]}件，每件￥${(e[1] / 100).toFixed(2)}<br>`;
      });
      return msg;
    },
    canPriceWhole() {
      return this.product.price_whole && typeof this.product.price_whole === 'object' && this.product.price_whole.length;
    },
    showType_List_ShowList() {
      return this.showType === 'list' && this.product === null;
    }
  },
  watch: {
    category(val) {
      if (val === null && this.$refs.category) {
        this.$refs.category.isFocused = false; // fix bug in selection.js:line 194
      }
      this.handleCategoryChange(val);
    },
    product(val) {
      if (val === null && this.$refs.product) {
        this.$refs.product.isFocused = false; // fix bug in selection.js:line 194
      }
      this.handleProductChange(val);
    },
    buyCount() {
      this.checkCoupon();
    }
  },
  mounted() {
    if (!this.shop) {
      // 未定义店铺信息, 说明是 默认店铺(shop_default) 只有查询和投诉选项
      if (window.$router.nav === 'buy') {
        window.$router.push('record');
      }
      return;
    }
    if (this.shopType === 'product') {
      this.categories = this.config.categories;
      this.category = this.config.categories[0];
      this.product = this.config.product;
      this.product.password = getParameterByName('p');
      this.product = this.config.product;
    } else {
      if (this.config.categories && this.config.categories.length) {
        this.categories = this.config.categories;
        if (this.showType === 'dropdown' || this.showType === 'button') {
          if (this.categories.length === 1 && !this.categories[0].password_open) {
            this.category = this.categories[0];
          }
        }
      } else {
        this.categories = [{
          id: -2,
          name: '没有在售商品'
        }];
        this.category = this.categories[0];
      }
    }
    this.onContactInput(false); // update field
    this.onContactExtInput(false); // update ext fields
  },
  methods: {
    renderDelta,
    async handleCategoryChange(category) {
      

      if (this.shopType === 'product') {
        return;
      }
      if (!category || category.id <= 0 || category.progress) {
        return;
      }
      this.product = null;
      if (!category.products) {
        let password = '';
        if (category.password_open) {
          try {
            password = (await this.$alert({
              message: '分类密码',
              title: '请输入分类密码',
              needInput: true,
              cancelBtnTitle: '取消'
            })).input;
          } catch (e) {
            console.log('cancel', e);
            this.category = null;
            return;
          }
        }
        try {
          this.$set(category, 'progress', true);
          this.$set(category, 'products', (await this.$http.post('product', {
            category_id: category.id,
            password
          })).data);
          category.password = password;
          if (!category.products || !category.products.length) {
            category.products = [{
              id: -2,
              name: '此分类下没有商品'
            }];
            this.product = category.products[0];
            return;
          }
        } catch (e) {
          this.category = null;
          return;
        } finally {
          this.$set(category, 'progress', false);
        }
      } else if (!category.products.length) {
        category.products = [{
          id: -2,
          name: '此分类下没有商品'
        }];
      }
      if (this.showType === 'dropdown' || this.showType === 'button') {
        if (category.products.length === 1 && !category.products[0].password_open) {
          this.product = category.products[0];
        }
      }
    },
    async handleProductChange(product) {
      
      if (!product || product.id <= 0) {
        return;
      }

      // 由于优惠券不一定所有商品都可用, 这时需要清空已输入的优惠券信息
      this.coupon = '';
      this.couponInfo = null;
      if (product.price_whole && typeof product.price_whole === 'string' && product.price_whole[0] === '[') {
        product.price_whole = JSON.parse(product.price_whole);
      }
      if (product.fields && typeof product.fields === 'string' && product.fields[0] === '{') {
        Object.assign(this.fields, JSON.parse(product.fields));
        this.fields.values = new Array(this.fields.ext.length);
        this.fields.errors = new Array(this.fields.ext.length);
      }
      this.onContactInput(false); // update field
      this.onContactExtInput(false); // update ext fields

      if (this.shopType !== 'product') {
        if (product.password_open && product.password === undefined) {
          // 开启了密码验证 且 没有验证过密码
          let password = '';
          try {
            this.$set(product, 'progress', true);
            password = (await this.$alert({
              message: '商品密码',
              title: '请输入商品密码',
              needInput: true,
              cancelBtnTitle: '取消'
            })).input;
          } catch (e) {
            this.$set(product, 'progress', false);
            this.product = null;
            return;
          }
          try {
            // 验证密码
            await this.$http.post('product/password', {
              product_id: product.id,
              password
            });
            product.password = password;
          } catch (e) {
            this.product = null;
            // 密码错误
          } finally {
            this.$set(product, 'progress', false);
          }
        }
      }
    },
    showDiscount(info) {
      let msg = '';
      info.price_whole.forEach(e => {
        msg += `满${e[0]}件，每件￥${(e[1] / 100).toFixed(2)}<br>`;
      });
      this.$alert(msg, '批发优惠');
    },
    onCountInput() {
      this.buyCount = this.buyCount ? parseInt(this.buyCount) : 0;
      if (this.buyCount < this.product.buy_min) {
        this.countErr = `该商品最少购买${this.product.buy_min}件，请重新选择`;
      } else if (this.buyCount > this.product.buy_max) {
        this.countErr = `该商品限购${this.product.buy_max}件，请重新选择`;
      } else {
        this.countErr = '';
      }
    },
    onCouponChange(e) {
      this.coupon = typeof e === 'string' ? e : e.target.value;
      if (!this.coupon || !this.coupon.length) {
        this.couponErr = '';
        this.couponInfo = null;
        return;
      }
      this.submitting = true;
      this.$http.post('coupon', {
        category_id: this.category.id,
        product_id: this.product.id,
        coupon: this.coupon
      }).then(e => {
        this.couponInfo = e.data;
        this.checkCoupon();
      }).catch(() => {
        this.couponInfo = null;
        this.couponErr = '优惠券信息无效';
      }).finally(() => {
        this.submitting = false;
      });
    },
    checkCoupon() {
      this.couponErr = '';
      if (this.couponInfo) {
        if (this.couponInfo.discount_type === DISCOUNT_TYPE_AMOUNT && this.priceAll < this.couponInfo.discount_val) {
          this.couponErr = `商品金额满￥${(this.couponInfo.discount_val / 100).toFixed(2)}可用`;
        }
      }
    },
    onContactExtInput(errorWhenEmpty = true) {
      let ret = true;
      if (this.fields.need_ext) {
        for (let i = 0; i < this.fields.values.length; i++) {
          if (this.fields.values[i] || errorWhenEmpty === false) {
            this.fields.errors[i] = null;
          } else {
            this.fields.errors[i] = `请填写 ${this.fields.ext[i]}`;
            ret = false;
          }
        }
      }
      return ret;
    },
    onContactInput(errorWhenEmpty = true) {
      if (!this.contact && errorWhenEmpty === false) {
        this.contactErr = '';
        return true;
      }
      if (this.fields.type === 'any') {
        if (!this.contact) {
          this.contactErr = '请填写您的联系信息，如QQ、邮箱、手机号等等，用于查询订单';
          return false;
        }
        if (this.contact.length < 6) {
          this.contactErr = '联系信息至少为6位';
          return false;
        }
      } else if (this.fields.type === 'email') {
        if (!this.contact) {
          this.contactErr = '请填写您的邮箱，用于查询订单';
          return false;
        }
        if (!validateEmail(this.contact)) {
          this.contactErr = '输入的邮箱格式不正确';
          return false;
        }
      } else if (this.fields.type === 'mobile') {
        if (!this.contact) {
          this.contactErr = '请填写您的手机号码，用于查询订单';
          return false;
        }
        if (!validateMobile(this.contact)) {
          this.contactErr = '输入的手机号格式不正确';
          return false;
        }
      } else if (this.fields.type === 'qq') {
        if (!this.contact) {
          this.contactErr = '请填写您的QQ号码，用于查询订单';
          return false;
        }
        if (this.contact.length < 5 || this.contact.length > 11 || !validatNumbers(this.contact)) {
          this.contactErr = '输入的QQ号码格式不正确';
          return false;
        }
      }
      this.contactErr = '';
      return true;
    },
    onQueryPasswordInput() {
      if (!this.query_password) {
        this.queryPassErr = '请输入订单查询密码';
        return false;
      }
      if (this.query_password.length < 6) {
        this.queryPassErr = '查询密码至少为6位';
        return false;
      }
      const top_100 = ['123456', 'password', '12345678', 'qwerty', '123456789', '12345', '1234', '111111', '1234567', 'dragon', '123123', 'baseball', 'abc123', 'football', 'monkey', 'letmein', '696969', 'shadow', 'master', '666666', 'qwertyuiop', '123321', 'mustang', '1234567890', 'michael', '654321', 'pussy', 'superman', '1qaz2wsx', '7777777', 'fuckyou', '121212', '000000', 'qazwsx', '123qwe', 'killer', 'trustno1', 'jordan', 'jennifer', 'zxcvbnm', 'asdfgh', 'hunter', 'buster', 'soccer', 'harley', 'batman', 'andrew', 'tigger', 'sunshine', 'iloveyou', 'fuckme', '2000', 'charlie', 'robert', 'thomas', 'hockey', 'ranger', 'daniel', 'starwars', 'klaster', '112233', 'george', 'asshole', 'computer', 'michelle', 'jessica', 'pepper', '1111', 'zxcvbn', '555555', '11111111', '131313', 'freedom', '777777', 'pass', 'fuck', 'maggie', '159753', 'aaaaaa', 'ginger', 'princess', 'joshua', 'cheese', 'amanda', 'summer', 'love', 'ashley', '6969', 'nicole', 'chelsea', 'biteme', 'matthew', 'access', 'yankees', '987654321', 'dallas', 'austin', 'thunder', 'taylor', 'matrix', 'minecraft'];
      if (top_100.includes(this.query_password)) {
        this.queryPassErr = '当前查询密码存在安全风险，请更换';
        return false;
      }
      this.queryPassErr = '';
      return true;
    },
    onSmsInput() {
      if (!this.smsTo) {
        this.smsErr = '请填写需要接受订单信息的手机号码';
        return false;
      }
      if (!validateMobile(this.smsTo)) {
        this.smsErr = '输入的手机号格式不正确';
        return false;
      }
      this.smsErr = '';
      return true;
    },
    onMailInput() {
      if (!this.mailTo) {
        this.mailErr = '请填写需要接受订单信息的邮箱';
        return false;
      }
      if (!validateEmail(this.mailTo)) {
        this.mailErr = '输入的邮箱格式不正确';
        return false;
      }
      this.mailErr = '';
      return true;
    },
    onSendSmsChange(val) {
      if (val && !this.smsTo) {
        if (validateMobile(this.contact)) {
          this.smsTo = this.contact;
        }
      }
    },
    onSendMailChange(val) {
      if (val && !this.mailTo) {
        if (validateEmail(this.contact)) {
          this.mailTo = this.contact;
        }
      }
    },
    calcContactExt() {
      let ret = {};
      for (let i = 0; i < this.fields.ext.length; i++) {
        ret[this.fields.ext[i]] = this.fields.values[i];
      }
      if (this.sendSms) {
        ret['_mobile'] = this.smsTo;
      }
      if (this.sendMail) {
        ret['_mail'] = this.mailTo;
      }
      if (MAIL_SEND_ORDER_USE_CONTACT && validateEmail(this.contact)) {
        ret['_mail'] = this.contact;
      }
      return JSON.stringify(ret);
    },
    async handlePay() {
      
      if (parseInt(this.buyCount) < 1) {
        return this.$alert('请输入正确的购买数量');
      }

      // noinspection JSCheckFunctionSignatures
      if (parseInt(this.product.count) <= 0) {
        return this.$alert('当前商品已售罄');
      }
      this.onCountInput(); // 可能已经修改了选择的商品, 这里刷新一下
      if (this.countErr) {
        console.log('handlePay', 'countErr');
        return this.$alert(this.countErr);
      }
      if (this.couponErr) {
        console.log('handlePay', 'couponErr');
        return this.$alert(this.couponErr);
      }

      // noinspection JSCheckFunctionSignatures
      if (parseInt(this.product.count) < parseInt(this.buyCount)) {
        return this.$alert('当前商品库存不足，请调整购买数目');
      }
      if (this.onContactInput() === false) {
        return this.$alert(this.contactErr, '联系方式无效');
      }
      if (ORDER_QUERY_PASSWORD && this.onQueryPasswordInput() === false) {
        return this.$alert(this.contactErr, '订单查询密码无效');
      }
      if (this.sendSms && this.onSmsInput() === false) {
        return this.$alert(this.smsErr, '手机号无效');
      }
      if (this.sendMail && this.onMailInput() === false) {
        return this.$alert(this.mailErr, '邮箱无效');
      }
      if (this.onContactExtInput() === false) {
        
        return this.$alert(this.fields.errors.filter(e => !!e).join('<br>'), '联系信息无效');
      }
      this.payConfirmShow = true;
    },
    async showWarningTip() {
      const warning = "\n        <div style=\"display: block;\">\n        本站仅提供自动发货服务，不做任何商品售后<br>\n        发现假卡或欺诈，请于<strong style=\"color: red\">当天来发卡平台投诉</strong>,\n        过期概不受理<br>\n        (下单后请于15分钟内完成支付)</div>\n        ";
      return this.$alert({
        message: warning,
        title: '重要声明',
        cancelBtnTitle: '返回',
        submitBtnTitle: '继续购买'
      });
    },
    async confirmPay() {
      if (window.config.captcha.scene.shop.buy) {
        try {
          await this.$captcha.refresh();
          await this.$captcha.verify();
          console.log('this.$captcha.verify successfully', this.$captcha.value);
        } catch (e) {
          console.log('this.$captcha.verify failed', e);
          if (e.message !== 'cancel') {
            this.$alert(e.message || '请重新验证', '验证码验证失败').finally(() => {
              this.$captcha.focus();
            });
          }
          return;
        }
      }

      // 这里的window.config.url是支付域名
      let customer = cookie.get('customer');
      if (!customer || customer.length !== 32) {
        customer = randomString(32);
        cookie.set('customer', customer, 2592000000);
      }
      let orderUrl = window.config.url + `/api/shop/buy?category_id=${this.category.id}&product_id=${this.product.id}`;
      if (this.category.password) {
        orderUrl += '&category_password=' + encodeURIComponent(this.category.password);
      }
      if (this.product.password) {
        orderUrl += '&product_password=' + encodeURIComponent(this.product.password);
      }
      if (this.coupon) {
        orderUrl += '&coupon=' + encodeURIComponent(this.coupon);
      }
      orderUrl += `&count=${this.buyCount}`;
      orderUrl += `&contact=${encodeURIComponent(this.contact)}&contact_ext=${encodeURIComponent(this.calcContactExt())}`;
      if (ORDER_QUERY_PASSWORD) {
        orderUrl += `&query_password=${encodeURIComponent(this.query_password)}`;
      }
      orderUrl += `&customer=${customer}&pay_id=${this.payway.id}`;
      if (window.config.captcha.scene.shop.buy) {
        orderUrl += '&' + param(this.$captcha.value, 'captcha');
      }
      window.open(orderUrl, '_blank');
      if (this.singleMode === 'flow') {
        this.payConfirmShow = false;
      }
      try {
        await this.$alert({
          
          message: '请在弹出的窗口中完成付款',
          title: '提示',
          cancelBtnTitle: '返回',
          submitBtnTitle: '已付款，查询订单'
        });
        window.$router.push('record', {
          tab: 'cookie'
        });
        await this.$nextTick();
        window.vm.$emit('goRecordCookie');
      } catch (e) {
        console.log('cancel');
      }
    }
  }
};
</script>


<style lang="scss" scoped>
  .mu-card {
    margin-bottom: 12px;
  }

  .pay-btn-container {
    margin-top: 12px;

    .btn-pay {
      background-color: #21b3f5;
      color: white;
      width: 132px;
    }
  }

  .discount-tip {
    position: absolute;
    font-size: 13px;
    background: #fff;
    color: rgba(0, 0, 0, 0.87);
    border: 1px solid #e0e0e0;
    padding: 8px 12px;
    z-index: 1;
  }

  .product-flow {
    margin-bottom: 12px;

    .label {
      color: rgba(0, 0, 0, 0.54);
      font-size: 12px;
      margin-bottom: 4px;
    }

    .info-item {
      white-space: nowrap;

      p {
        margin: 0;
        display: inline-block;
        width: 220px;
      }

      @media only screen and (max-width: 767px) {
        p {
          width: calc(50% - 2px);
        }
      }

      .title {
        display: inline-block;
        width: 72px;
        white-space: nowrap;
        text-align: right;
        margin-right: 6px;
      }

      .val {
        display: inline-block;
        color: #03a9f4;
      }
    }

    .price-item {
      display: inline-block;
      line-height: 32px;

      .money {
        font-size: 18px;
        color: #ff7a22;
      }

      .money-discount, .money-fee {
        font-size: 14px;
      }
    }


    .pay-way-item {
      margin-top: 8px;
      margin-bottom: 24px;
    }
  }

  /* 单商品, 左右并排模式 */
  .product-flex {
    display: flex;
    flex-direction: row-reverse;

    // 单商品时, 左右并排显示
    .product-description {
      width: 44%;
      max-width: 420px;
    }

    .product-info {
      width: 44%;
      flex-grow: 1;

      .product-name {
        padding: 24px 0 0;

        & > h2 {
          margin: 0;
          font-size: 24px;
          line-height: 30px;
          font-weight: normal;
        }
      }

      & > .info-item {
        white-space: nowrap;
        line-height: 32px;

        .info-title {
          display: inline-block;
          white-space: nowrap;
        }

        .info-val {
          display: inline-block;
          white-space: nowrap;
          margin-left: 4px;
        }
      }


      & > .price-container {
        margin-top: 12px;
      }

      .price-whole-container {
        margin-left: 16px;
        display: inline-block
      }

      .price {
        color: #ff7a22;
        font-size: 16px;
      }

      .count-left {
        display: inline-block;
        margin-left: 8px;
      }

      .money-total {
        display: inline-block;

        .money {
          font-size: 16px;
          color: #ff7a22;
        }

        .money-discount, .money-fee {
          font-size: 14px;
        }
      }

      .info-delivery {
        .mu-chip {
          cursor: default;
          user-select: none;
          font-size: 12px;
          line-height: 18px;
        }
      }

      .input-fields {
        .mu-input {
          width: 100%;
        }
      }

      .input-coupon {
        .mu-input {
          width: 100%;
        }
      }

      .input-count {
        .mu-input {
          width: 70%;
        }
      }

      .pay-btn-container {
        margin-bottom: 24px;

        .btn-pay {
          background-color: #da3b01;
        }

        .btn-cancel {
          margin-left: 14px;
          width: 86px;
        }
      }
    }

    .product-gap {
      width: 2%;
      max-width: 26px;
      vertical-align: top;
    }

    @media only screen and (max-width: 767px) {
      & {
        display: block;
      }

      .product-info {
        flex: none;
        width: 100%;
        max-width: none;

        .product-name {
          padding: 4px 0 0;
        }
      }

      // 手机端, 隐藏左侧的商品详情, 并且展示 description-mobile
      .product-description, .product-gap {
        display: none;
      }
    }
  }

  .description-mobile {
    display: none;

    .description-title {
      h3 {
        margin: 0;
        font-weight: normal;
      }
    }
  }

  @media only screen and (max-width: 767px) {
    .description-mobile {
      display: block;
      margin-top: 12px;
    }
  }


  /* 购买确认框 */
  .confirm-item {
    

    .title {
      display: inline-block;
      margin-right: 4px;
      white-space: nowrap;
    }

    .money {
      font-size: 14px;
      color: #ff7a22;
    }

    .money-discount, .money-fee {
      font-size: 12px;
    }
  }

  .pay-confirm-card {
    .confirm-title {
      h3 {
        margin: 0;
        font-weight: normal;
      }
    }

    .confirm-btn-container {
      margin-top: 12px;
      margin-bottom: 24px;

      .btn-confirm {
        background-color: #21b3f5;
        color: white;
        width: 132px;
      }

      .btn-cancel {
        margin-left: 14px;
        width: 86px;
      }
    }
  }

</style>

<style lang="scss">
  .buy-count {
    .mu-text-field-help > div {
      margin-right: -800px;
    }
  }

  .info-item.input-item {

    .mu-input-label {
      transform: none;
      color: rgba(0, 0, 0, 0.87);
      font-size: 14px;
    }

    .mu-input-line {
      background-color: rgba(0, 0, 0, 0.3);
    }
  }
</style>
