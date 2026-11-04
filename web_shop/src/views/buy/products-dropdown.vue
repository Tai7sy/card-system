<template>
  <div>
    <mu-card class="my-card white-background">
      <mu-card-text>
        <mu-select ref="category" name="category" label="请选择商品分类" class="shop-select"
                   :value="category"
                   @change="v => chooseCategory(v)"
                   :filterable="categories.length > 10"
                   :errorText="categoryErr">
          <mu-option v-for="item in categories" :value="item" :key="item.id" :label="item.name">
            <mu-icon v-if="item.password_open" value="lock" :size="12" style="position: absolute; left: 2px"/>
            {{item.name}}
          </mu-option>
        </mu-select>

        <mu-circular-progress v-if="category && category.progress" :size="40" style="width: 100%; text-align: center"/>

        <transition name="top-slide">
          <mu-select ref="product" :errorText="productErr"
                     :filterable="categories.length > 10"
                     v-if="category && category.products && category.products.length"
                     :value="product"
                     @change="v => chooseProduct(v)"
                     name="product_id"
                     label="请选择商品"
                     class="shop-select">
            <mu-option v-for="item in category.products" :value="item" :key="item.id" :label="item.name">
              <mu-icon v-if="item.password_open" value="lock" :size="12" style="position: absolute; left: 2px"/>
              {{item.name}}
            </mu-option>
          </mu-select>
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
    name: "ProductsDropdown",
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
  .shop-select {
    width: 100%;
    margin-bottom: 0;
  }
</style>
