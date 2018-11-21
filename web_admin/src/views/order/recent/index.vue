<template>
  <order-list :recentDay="7"></order-list>
</template>

<script>
  import orderList from '../list/index'

  export default {
    components: { orderList }
  }
</script>
