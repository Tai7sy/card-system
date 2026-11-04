<template>
  <div>
    <mu-card class="my-card white-background">
      <mu-card-text>
        <div v-if="product !== null && product.id > 0">
          <slot name="info"/>
        </div>
      </mu-card-text>
    </mu-card>

  </div>
</template>

<script>
  export default {
    name: "ProductsNull",
    props: ['product'],
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
