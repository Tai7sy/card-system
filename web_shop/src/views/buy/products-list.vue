<template>
  <div>
    <div v-show="product === null">
      <mu-card v-for="c in categories" :key="'category_list_'+c.id"
               class="my-card white-background">
        <mu-card-title class="list-select-title" :title="c.name"></mu-card-title>

        <mu-card-text ref="product" v-if="isMobile" class="list-select list-select-mobile">
          <table>
            <tbody>
            <tr v-if="c.password_open && !c.products" style="text-align: center">
              <td>
                <mu-icon value="lock" :size="12"/>&nbsp;
                <a @click="chooseCategory(c)" class="pass">输入密码查看</a>
                <mu-circular-progress v-if="c.progress" :size="40" style="width: 100%; text-align: center"/>
              </td>
            </tr>
            <tr v-if="!c.password_open && (c.products && c.products.length === 0)"
                style="text-align: center">
              <td>
                暂无商品
              </td>
            </tr>
            <tr v-for="item in c.products" :key="'product_'+c.id+'_'+item.id">
              <td>
                <div>
                  {{item.name}}
                  <mu-icon v-if="item.password_open" value="lock" :size="12"/>
                </div>
                <div class="count-mobile">
                  <mu-chip color="#00b5ff" text-color="#fff">库存&nbsp;{{item.count2}}</mu-chip>
                </div>
                <div class="delivery-mobile" v-if="PRODUCT_MANUAL">
                  <mu-chip v-if="item.delivery===0" color="#00b5ff" text-color="#fff">自动发货</mu-chip>
                  <mu-chip v-else color="#dd9f00" text-color="#fff">手动充值</mu-chip>
                </div>
                <div class="price-mobile">{{item.price | moneyFilter | moneyFormatterFilter}}</div>
                <div class="buy-btn-mobile">
                  <a @click="chooseProduct(c,item)">购买</a>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </mu-card-text>

        <mu-card-text ref="product" v-else class="list-select list-select-pc">
          <table>
            <thead>
            <tr>
              <th style="width: 80%">商品名称</th>
              <th>单价</th>
              <th>库存</th>
              <th width="50" style="text-align:right">操作</th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="c.password_open && !c.products" style="text-align: center">
              <td colspan="4">
                <mu-icon value="lock" :size="12"/>&nbsp;
                <a @click="chooseCategory(c)" class="pass">输入密码查看</a>
                <mu-circular-progress v-if="c.progress" :size="40" style="width: 100%; text-align: center"/>
              </td>
            </tr>
            <tr v-if="!c.password_open && (c.products && c.products.length === 0)"
                style="text-align: center">
              <td colspan="4">
                暂无商品
              </td>
            </tr>
            <tr v-for="item in c.products" :key="'product_'+c.id+'_'+item.id">
              <td>
                {{item.name}}
                <mu-icon v-if="item.password_open" value="lock" :size="12"/>
              </td>
              <td>{{item.price | moneyFilter | moneyFormatterFilter}}</td>
              <td>{{item.count2}}</td>
              <td style="text-align:right">
                <a @click="chooseProduct(c,item)">购买</a>
              </td>
            </tr>
            </tbody>
          </table>
        </mu-card-text>
      </mu-card>
    </div>

    <mu-card v-if="product !== null && product.id > 0" class="my-card white-background">
      <mu-card-text>
        <mu-circular-progress v-if="product.progress" :size="40" style="width: 100%; text-align: center"/>
        <div v-else>
          <!-- 用于flex返回到列表 , 右上角关闭按钮 -->
          <a style="float: right; margin: -14px -14px 0 0;">
            <mu-button color="red" icon @click="$emit('update:product', null)">
              <mu-icon value="close"></mu-icon>
            </mu-button>
          </a>

          <slot name="info"/>

        </div>
      </mu-card-text>
    </mu-card>
  </div>
</template>

<script>
  import device from '~/utils/device'

  // 是否开启手动发货功能
  const PRODUCT_MANUAL = window.config.functions && window.config.functions.includes('product_manual');

  export default {
    name: "ProductsList",
    props: ['categories', 'category', 'product'],
    data () {
      return {
        isMobile: device.isMobile(),
        PRODUCT_MANUAL
      }
    },
    methods: {
      chooseCategory (category) {
        this.$emit('update:category', category);
      },
      async chooseProduct (category, product) {
        this.$emit('update:category', category);
        await this.$nextTick();
        this.$emit('update:product', product);
      },
    }
  }
</script>

<style lang="scss" scoped>

  .my-card {
    margin-bottom: 12px
  }

  /*列表式*/
  .list-select-title {
    .mu-card-title {
      font-size: 15px;
      line-height: 15px;


      

    }
  }

  .list-select {

    table {
      width: 100%;
      border-collapse: collapse;
      border-top: 1px solid #f2f2f2;

      th, td {
        font-weight: inherit;
        padding: 8px;
        min-width: 58px;
        overflow: auto;
      }

      th {
        background-color: #f6f6f6;
      }

      tr {
        text-align: left;
        height: 40px;
        border-bottom: 1px solid #f2f2f2;
        transition: background-color 0.5s ease;
      }

      & > tbody > tr {
        & > td {
          min-width: 48px;
        }

        a {
          width: 100%;
          height: 100%;
          display: inline-block;
          cursor: pointer;

          &:hover {
            text-decoration: underline;
          }
        }

        .pass {
          width: auto;
          cursor: pointer;

          &:hover {
            text-decoration: underline;
          }
        }
      }
    }
  }

  .list-select-pc {
    margin: 0 -2px;
    padding-top: 0;
  }

  .list-select-mobile {
    padding-top: 0;

    .mu-chip {
      cursor: default;
      user-select: none;
      font-size: 12px;
      line-height: 18px;
    }

    .count-mobile {
      display: inline-block;
    }

    .delivery-mobile {
      display: inline-block;
      margin-left: 8px;
    }

    .price-mobile {
      display: inline-block;
      margin-left: 8px;
      color: red;
    }

    .buy-btn-mobile {
      display: inline-block;
      float: right;
    }
  }
</style>

<style lang="scss">
  .s-opacity-75 {
    .list-select {
      table {
        border-top: 1px solid #f2f2f299;

        th {
          background-color: #f2f2f266;
        }

        tr {
          border-bottom: 1px solid #f2f2f299;
        }
      }
    }
  }
</style>
