<template>
  <div>
    <mu-card class="my-card white-background">
      <mu-card-text>
        <div ref="category" class="button-select">
          <div class="label mu-text-field-label">请选择商品分类</div>
          <mu-ripple v-for="item in categories" :key="item.id"
                     class="button-select-item" :class="{checked: category===item}"
                     @click="chooseCategory(item)">{{item.name}}
            <mu-icon v-if="item.password_open" value="lock" :size="12"/>
          </mu-ripple>
          <div class="input-help" style="color: #f44336;" v-if="categoryErr">
            <div>{{categoryErr}}</div>
          </div>
        </div>
        <mu-circular-progress v-if="category && category.progress" :size="40" style="width: 100%; text-align: center"/>
        <transition name="top-slide">
          <div ref="product" class="button-select"
               v-if="category && category.products && category.products.length">
            <div class="label mu-text-field-label">请选择商品</div>
            <mu-ripple v-for="item in category.products" :key="item.id"
                       class="button-select-item" :class="{checked: product===item}"
                       @click="chooseProduct(item)">{{item.name}}
              <mu-icon v-if="item.password_open" value="lock" :size="12"/>
            </mu-ripple>
            <div class="input-help" style="color: #f44336;" v-if="productErr">
              <div>{{productErr}}</div>
            </div>
          </div>
        </transition>

        <transition name="top-slide">
          <div v-if="product !== null && product.id > 0">
            <mu-circular-progress v-if="product.progress" :size="40" style="width: 100%; text-align: center"/>
            <div v-else>
              <div class="label mu-text-field-label">
                商品信息
              </div>
              <slot name="info"/>
            </div>
          </div>
        </transition>

      </mu-card-text>
    </mu-card>

  </div>
</template>

<script>
  export default {
    name: "ProductsButton",
    props: ['categories', 'category', 'product', 'categoryErr', 'productErr'],
    methods: {
      chooseCategory (category) {
        this.$emit('update:category', category);
      },
      chooseProduct (product) {
        this.$emit('update:product', product);
      },
    }
  }
</script>

<style lang="scss" scoped>
  /* 按钮式 */
  .button-select {
    margin-bottom: 4px;

    .label {
      color: rgba(0, 0, 0, 0.54);
      font-size: 12px;
      margin-bottom: 4px;
    }

    .button-select-item {
      position: relative;
      border-radius: 2px;
      border: 1px solid transparent;
      display: inline-block;
      user-select: none;
      cursor: pointer;
      padding: 4px 8px;
      margin: 0 12px 8px 0;
      transition: all 0.3s ease;
      box-shadow: 0 1px 4px 0 rgba(0, 0, 0, .2);

      &:hover {
        box-shadow: 0 1px 4px 0 rgba(3, 169, 244, .3);
      }

      &.checked {
        border: 1px solid #03a9f4;
      }
    }

    .input-help {
      font-size: 12px;
      line-height: 12px;
    }
  }
</style>
