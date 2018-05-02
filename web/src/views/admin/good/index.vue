<template>
  <div class="content-wrapper">

    <mu-dialog :open="!!process" title="提示">
      <mu-circular-progress :size="40" style="vertical-align:middle;"/>
      <span v-html="process"
            style="line-height: 40px;display:inline-block;margin-left: 24px"></span>
    </mu-dialog>

    <div v-show="show === 'list'">
      <div class="table-title">

        <mu-float-button icon="add" mini class="float-right" @click="handleAdd" style="margin-right: 8px"/>
        <mu-float-button icon="refresh" mini class="float-right" @click="getList" style="margin-right: 8px"/>
      </div>

      <div class="table-container">
        <mu-table ref="table" :showCheckbox="false" :selectable="false">
          <mu-thead>
            <mu-tr>
              <mu-th style="width: 120px">商品名称</mu-th>
              <mu-th></mu-th>
              <mu-th style="width: 120px">类别</mu-th>
              <mu-th></mu-th>
              <mu-th style="width: 80px">卡密</mu-th>
              <mu-th style="width: 80px">库存</mu-th>
              <mu-th style="width: 80px">状态</mu-th>
              <mu-th style="width: 80px">价格</mu-th>
              <mu-th style="width: 130px">操作</mu-th>
            </mu-tr>
          </mu-thead>
          <mu-tbody>
            <mu-tr v-for="item in list.data" :key="item.id">
              <mu-td class="over-ellipsis">{{item.name}}</mu-td>
              <mu-td></mu-td>
              <mu-td class="over-ellipsis">{{item.group?item.group.name:'未知'}}</mu-td>
              <mu-td></mu-td>
              <mu-td>
                <a class="cursor-pointer" @click="handleViewGood(item)" style="width: 100%;display: inline-block">
                  {{item.cards_count}}
                </a>
              </mu-td>
              <mu-td>{{item.sold_count}}&nbsp;/&nbsp;{{item.all_count}}</mu-td>
              <mu-td>{{item.enabled?'已启用':'禁用'}}</mu-td>
              <mu-td>{{item.price | moneyFilter | moneyFormatterFilter}}</mu-td>

              <mu-td>
                <a class="cursor-pointer" @click="handleEdit(item)">编辑</a>&nbsp;
                <a class="cursor-pointer" @click="handleEnabled(item)">{{item.enabled?'禁用':'启用'}}</a>&nbsp;
                <a class="cursor-pointer" @click="handleDelete(item)">删除</a>&nbsp;
              </mu-td>
            </mu-tr>
          </mu-tbody>
        </mu-table>
        <mu-pagination :total="list.total" :current="list.current_page" :pageSize="list.per_page"
                       :showSizeChanger='true' @pageSizeChange="handlePageSizeChange"
                       @pageChange="handlePageChange" style="float: right; margin-top: 12px">
        </mu-pagination>
      </div>
    </div>

    <div v-if="show === 'card' && goodInfo">
      <cards :good="goodInfo" @close="goodCloseCallback"></cards>
    </div>


    <mu-dialog :open="!!act" title="编辑信息" @close="editCallback" scrollable>

      <mu-select-field v-model="editForm.group_id" label="请选择分类" class="edit-input">
        <mu-menu-item v-for="item in groups" :value="item.id" :title="item.name" :key="item.id"/>
      </mu-select-field>
      <mu-text-field label="名称" hintText="请输入名称" labelFloat
                     v-model="editForm.name" class="edit-input"/>
      <mu-text-field label="描述" hintText="请输入描述" labelFloat multiLine :rows="2" :rowsMax="100"
                     v-model="editForm.description" class="edit-input"/>
      <mu-text-field label="库存" hintText="请输入库存" type="number" labelFloat
                     v-model="editForm.all_count" class="edit-input"/>

      <mu-text-field label="价格" hintText="请输入价格/单位元" labelFloat
                     v-model="editForm.price"/>

      <mu-switch label="启用" labelLeft v-model="editForm.enabled_bool"/>
      <mu-flat-button slot="actions" @click="editCallback" primary label="取消"/>
      <mu-flat-button slot="actions" primary @click="handleEditSubmit" label="确定"/>
    </mu-dialog>
  </div>
</template>

<script>
  import myFunc from '../../../utils/myfunc.js'
  import Cards from './card';

  export default {
    components: { Cards },
    data () {
      const initForm = {
        id: '',
        group_id: -1,
        name: '',
        description: '',
        all_count: '',
        price: '',
        enabled: 0,
        enabled_bool: false
      };
      return {
        process: false,
        show: 'list',
        goodInfo: null,
        list: {
          current_page: 1,
          per_page: 10,
          total: 0,
          data: []
        },
        act: null,
        groups: [{
          id: -1,
          name: '加载中'
        }],
        initForm,
        editForm: Object.assign({}, initForm)
      }
    },
    mounted () {
      this.checkLogin();
      this.getList();
      this.getGroups();
    },
    methods: {
      checkLogin () {
        if (!myFunc.cookie.get('login')) {
          this.$router.push('/admin/login');
        }
      },
      getList () {
        this.process = '加载中...'
        this.$http.post('admin/good/get', {
          page: this.list.current_page,
          size: this.list.per_page
        }).then(e => {
          e.data.per_page = parseInt(e.data.per_page);
          this.list = e.data
          this.process = false
        }).catch(() => {
          this.process = false
        });
      },
      handlePageChange (v) {
        this.list.current_page = v;
        this.getList()
      },
      handlePageSizeChange (v) {
        this.list.per_page = v;
        this.getList()
      },
      getGroups () {
        this.$http.post('admin/group/get').then(e => {
          this.groups = e.data
        })
      },
      handleAdd () {
        if (this.groups.length === 0) {
          this.$alert('请先添加商品分类')
          return
        }
        this.editForm = Object.assign({}, this.initForm)
        this.act = 'add'
      },
      handleEdit (item) {
        this.act = 'edit'
        const buf = Object.assign({}, item);
        buf.price = (buf.price / 100).toFixed(2);
        buf.enabled_bool = buf.enabled === 1
        Object.assign(this.editForm, buf);
      },
      editCallback () {
        this.act = null
      },
      handleEditSubmit () {
        let editInfo = Object.assign({}, this.editForm);
        editInfo.enabled = editInfo.enabled_bool ? '1' : '0';
        editInfo.price *= 100;
        this.$http.post('admin/good/edit', editInfo).then(() => {
          this.getList();
          this.act = null;
        })
      },
      handleEnabled (item) {
        let enabled = item.enabled ? 0 : 1;
        this.$http.post('admin/good/enabled', {
          id: item.id,
          enabled
        }).then(() => {
          item.enabled = enabled;
        })
      },
      handleDelete (item) {
        if (confirm('是否删除?')) {
          this.$http.post('admin/good/delete', {
            id: item.id
          }).then(() => {
            this.getList()
          })
        }
      },
      handleViewGood (item) {
        this.show = 'card';
        this.goodInfo = item;
      },
      goodCloseCallback () {
        this.show = 'list';
      }
    }
  }
</script>

<style lang="less" scoped>
  .edit-input {
    vertical-align: bottom;
  }

  .table-title {
    width: 100%;
    height: 44px;
  }

  .table-container {
    overflow: auto;
    .table {
      min-width: 650px;
    }
    .today-tip {
      word-break: normal;
      width: auto;
      display: inline-block;
      white-space: pre-wrap;
      word-wrap: break-word;
    }
  }

  .mu-td, .mu-th {
    padding: 0;
  }

</style>
