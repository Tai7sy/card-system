<template>
  <div class="content">
    <form action="/buy" method="post" target="_target" ref="buy">
      <mu-select-field name="group_id" v-model="group" label="请选择商品类别" class="shop-select" @change="handleGroupSelect">
        <mu-menu-item v-for="item in groups" :value="item.id" :title="item.name" :key="item.id"/>
      </mu-select-field>
      <mu-circular-progress v-if="process.group" :size="40" style="width: 100%; text-align: center"/>
      <transition name="top-slide">
        <mu-select-field name="good_id" v-show="group && goods.length" v-model="good" label="请选择商品" class="shop-select"
                         @change="handleGoodSelect">
          <mu-menu-item v-for="item in goods" :value="item.id" :title="item.name" :key="item.id"/>
        </mu-select-field>
      </transition>
      <transition name="top-slide">
        <div v-if="selectGood && selectGood.description" class="description">
          <mu-card style="margin-bottom: 12px;">
            <mu-card-text v-html="selectGood.description"></mu-card-text>
          </mu-card>


          <mu-text-field name="count" labelFloat label="购买数目" v-model="buyCount" style="width: 64px"/>
          <span class="price-area">
            <span class="money">{{selectGood.price * buyCount | moneyFilter | moneyFormatterFilter}}</span>
          </span>

          <mu-text-field name="email" type="email" hintText="请输入您的收货邮箱" v-model="email" style="width: 100%"/>

          <mu-circular-progress v-if="process.pay" :size="40" style="width: 100%; text-align: center"/>
          <transition name="top-slide">
            <div class="recharge-way" v-show="!process.pay">
              <div>
                <div v-for="way in pays" class="payway"
                     :class="{checked:pay === way.id}"
                     @click="pay = way.id">
                  <img :src="way.img"/>
                  <p>{{way.name}}</p>
                </div>
                <input name="pay_id" v-model="pay" hidden title="pay_id"/>
              </div>
              <div class="pay-btn">
                <mu-raised-button label="支付" @click="handlePay"/>
              </div>

            </div>
          </transition>
        </div>
      </transition>
    </form>

  </div>
</template>

<script>
  export default {
    data () {
      return {
        process: {
          group: false,
          pay: false
        },
        group: -1,
        groups: [{
          id: -1,
          name: '加载中'
        }],
        good: -1,
        goods: [],
        selectGood: null,
        buyCount: '1',
        email: '',
        pay: -1,
        pays: []
      }
    },
    mounted () {
      this.getGroups()
    },
    methods: {
      getGroups () {
        this.$http.post('shop/group').then(e => {
          this.groups = e.data;
          this.group = -1;
          if (this.groups.length) {
            this.groups.splice(0, 0, {
              id: -2,
              name: '请选择商品分类'
            })
          } else {
            this.groups = [{
              id: -2,
              name: '暂时没有商品哦'
            }];
          }
          this.group = -2;
        });
      },
      handleGroupSelect (v) {
        if (v < 0) {
          this.goods = []
          this.selectGood = null
          return
        }
        this.process.group = true;
        this.$http.post('shop/good', {
          group_id: v
        }).then(e => {
          this.process.group = false;
          this.goods = e.data;
          this.good = -1;
          if (this.goods.length) {
            this.goods.splice(0, 0, {
              id: -2,
              name: '请选择商品'
            })
          } else {
            this.goods = [{
              id: -2,
              name: '暂时没有商品哦'
            }];
          }
          this.good = -2;
        }).catch(() => {
          this.process.group = false;
        });
      },
      handleGoodSelect (v) {
        if (v < 0) {
          this.selectGood = null
          return
        }
        this.selectGood = this.goods.filter(e => e.id === v)[0];
        this.getPays();
      },
      getPays () {
        if (this.pays.length) {
          return;
        }
        this.process.pay = true;
        this.$http.post('shop/pay').then(e => {
          this.pays = e.data
          this.process.pay = false;
        }).catch(() => {
          this.process.pay = false;
        })
      },
      handlePay () {
        if (parseInt(this.buyCount) < 1) {
          return this.$alert('请输入正确的购买数量');
        }

        if (this.selectGood.available < this.buyCount) {
          return this.$alert(`当前商品仅剩${this.selectGood.available}件，请调整购买数目`);
        }

        if (this.email.length === 0) {
          return this.$alert('请输入您的收货邮箱，商品付款后将发送到您的邮箱');
        }
        if (this.pay < 0) {
          return this.$alert('请选择支付方式');
        }

        this.$refs['buy'].submit();
      }
    }
  }
</script>


<style lang="less" scoped>
  .content {
    margin: 12px 8px 8px;
  }

  .shop-select {
    width: 100%;
    margin-bottom: 0;
  }

  .top-slide-enter-active {
    transition: all .5s;
  }

  .top-slide-leave-active {
    transition: all .5s;
  }

  .top-slide-enter, .top-slide-leave-active {
    opacity: 0;
  }

  .top-slide-enter {
    transform: translateY(-32px);
  }

  .top-slide-leave-active {
    position: absolute;
    transform: translateY(-32px);
  }

  .description {
    margin-top: 0;
  }

  .recharge-way {
    margin-bottom: 24px;
    h2 {
      margin-top: 0;
    }
  }

  .price-area {
    display: inline-block;
    line-height: 32px;
    .money {
      font-size: 18px;
      color: #ff7a22;
    }
  }

  .payway {
    display: inline-block;
    margin: 4px 12px 4px 0;
    padding: 4px;
    cursor: pointer;
    img {
      height: 48px;
    }
    p {
      text-align: center;
      margin: 0;
    }

    border: 1px solid transparent;
    &.checked {
      border: 1px solid #03a9f4;
    }
  }

  .pay-btn {
    margin-top: 12px;
    // text-align: right;
  }
</style>
