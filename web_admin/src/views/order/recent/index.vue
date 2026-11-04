<template>
  <order-list :recentDay="7"></order-list>
</template>

<script>
  import orderList from '../list/index.vue'

  export default {
    components: { orderList }
  }
</script>
