<template>
  <card-list :onlyTrashed="true"></card-list>
</template>

<script>
  import cardList from '../list/index'

  export default {
    components: { cardList }
  }
</script>
