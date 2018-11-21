<template>
  <order-list :onlyProfit="true"></order-list>
</template>

<script>
  import orderList from '../list/index'

  export default {
    components: { orderList }
  }
</script>
