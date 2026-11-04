<template>
  <card-list :_trashed="true"></card-list>
</template>

<script>
  import cardList from '../list/index'

  export default {
    components: { cardList }
  }
</script>
