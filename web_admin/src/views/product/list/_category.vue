<template>
  <el-dialog title="修改商品分类" :visible.sync="visible" class="product-category" :close-on-click-modal="false">

    <el-form ref="form" :model="form" :label-position="isMobile?'top':''" label-width="90px"
             :rules="formRules">

      <el-form-item label="分类" prop="category_id" :class="isMobile?'item-container':''"
                    :style="isMobile?'margin-top: -32px':''">
        <el-select v-model="form.category_id"
                   allow-create filterable
                   no-data-text="请输入分类名称" style="display: block">
          <el-option v-for="item in categories_" :value="item.id" :key="item.id" :label="item.name"/>
        </el-select>
      </el-form-item>
    </el-form>

    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">确认修改
      </el-button>
    </div>
  </el-dialog>
</template>

<script>

  import { category_edit } from '~/api/product'
  import { getList as getCategoryList } from '~/api/category'

  export default {
    props: {
      info: {},
      categories: {},
      loadCategory: {}
    },
    data () {
      return {
        loading: false,
        isMobile: this.$store.state.app.isMobile,
        visible: true,
        form: {
          id: this.info.id,
          category_id: this.info.category_id,
        },
        formRules: {
          category_id: [
            { required: true, message: '请选择商品分类', trigger: 'blur' },
          ],
        },
        categories_: []
      }
    },
    watch: {
      visible (val) {
        if (!val) {
          this.$emit('close', false);
        }
      }
    },
    mounted () {
      if (!this.loadCategory) {
        const where = { search: 'simple', val: 'list', };
        if (this.info) {
          where.user_id = this.info.user_id;
        }
        getCategoryList(where).then(ret => {
          this.categories_ = ret.data;
        })
      } else {
        this.categories_ = this.categories;
      }
    },
    methods: {
      handleSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;

          let category_id = this.form.category_id;
          const isAddCategory = typeof category_id === 'string';
          if (isAddCategory) {
            category_id = '+' + category_id;
          }
          category_edit(this.form.id, category_id).then(res => {
            this.loading = false;
            this.$notify({
              title: '提示',
              message: '修改商品分类成功',
              type: 'success',
              duration: 3000
            });
            this.$emit('close', {
              category: res.data,
              isAddCategory
            });
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleClose (success = false) {
        this.visible = false;
        this.$emit('close', success);
      }
    }
  }
</script>

<style lang="scss">
  .product-category{
    .item-container {
      .el-form-item__label {
        padding-bottom: 0 !important;
        margin-bottom: -4px !important;
      }

      margin-bottom: 8px;

      .el-select, .el-checkbox {
        display: block;
      }

      .el-checkbox + .el-checkbox {
        margin-left: 0;
      }

      .el-radio-group {
        display: block;
      }
    }
  }
</style>
