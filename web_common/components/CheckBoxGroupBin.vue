<template>
  <el-checkbox-group class="el-checkbox-bin" v-model="checks" placeholder="请选择" @change="handleChange" :size="size">
    <el-checkbox-button v-for="item in items" :label="item.value" :key="item.value">
      {{item.text}}
    </el-checkbox-button>
  </el-checkbox-group>
</template>

<script>
  export default {
    name: 'CheckBoxGroupBin',
    props: {
      value: {},
      items: {
        type: Array
      },
      size: {}
    },
    data () {
      return {
        values: this.items.map(e => e.value),
        checks: []
      }
    },
    watch: {
      value (val) {
        console.log('CheckBoxGroupBin.watch', val);
        this.parseValue(val);
        console.log('CheckBoxGroupBin.watch.checks', JSON.stringify(this.checks));
      }
    },
    mounted () {
      this.parseValue(this.value)
    },
    methods: {
      parseValue (value) {
        this.checks = (() => {
          const ret = [];
          this.values.forEach(e => {
            if (value & e) ret.push(e)
          });
          return ret
        })()
      },
      handleChange (checks) {
        console.log('CheckBoxGroupBin.handleChange', JSON.stringify(checks));
        const tmp = this.checks.reduce((a, b) => a | b, 0); // empty will raise a error without 0
        this.$emit('input', tmp);
        this.$emit('change', tmp);
      }
    }
  }
</script>

<style lang="scss">
  .el-checkbox-bin {
    .el-checkbox-button--mini {
      .el-checkbox-button__inner {
        padding: 4px 8px !important;
      }
    }

    .el-checkbox-button.is-checked .el-checkbox-button__inner {
      background-color: #4cd964 !important;
      border-color: #4cd964 !important;
      box-shadow: -1px 0 0 0 #aad9ad !important;
    }
  }
</style>

