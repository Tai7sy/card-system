<template>
  <el-dialog title="编辑卡号" :visible.sync="visible">
    <el-form ref="form" :model="form" label-width="80px" :rules="formRules">
      <el-form-item label="卡号" prop="card">
        <el-input type="text" placeholder="请输入卡号" v-model="form.card">
        </el-input>
      </el-form-item>

      <!--el-form-item label="状态">
        <el-radio-group v-model="form.status" size="small">
          <el-radio-button :label="cardStatus.STATUS_NORMAL">未使用</el-radio-button>
          <el-radio-button :label="cardStatus.STATUS_SOLD" class="el-radio-gray">已使用</el-radio-button>
        </el-radio-group>
      </el-form-item-->

      <el-form-item label="类型">
        <el-radio-group v-model="form.type" size="small">
          <el-radio-button :label="cardType.TYPE_ONETIME">一次性</el-radio-button>
          <el-radio-button :label="cardType.TYPE_REPEAT">重复使用</el-radio-button>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="可售" prop="count_all" v-if="form.type === cardType.TYPE_REPEAT"
                    :rules="[{ required: true, message: '请输入可售总次数', trigger: 'blur' },
                    { type: 'number', min: 1 ,max: 10000000, message: '可售总次数不能超过10000000'}]">
        <el-input type="number" placeholder="请输入可售总次数" v-model.number="form.count_all"/>
        <span>当前已售出&nbsp;{{form.count_sold}}次</span>
      </el-form-item>

    </el-form>
    <div slot="footer" class="dialog-footer">
      <el-button @click.native="handleClose(false)">取消</el-button>
      <el-button type="primary" @click.native="handleSubmit" :loading="loading">确认编辑
      </el-button>
    </div>
  </el-dialog>
</template>

<script>
  import { cardStatus, cardType, edit as card_edit } from 'api/card'

  export default {
    props: {
      info: {}
    },
    data () {
      return {
        loading: false,
        visible: true,
        cardStatus, cardType,
        form: {
          id: 0,
          product_id: 0,
          card: '',
          // status: cardStatus.STATUS_NORMAL,
          type: cardType.TYPE_ONETIME,
          count_all: null,
          count_sold: null,
        },
        formRules: {
          card: [
            { required: true, message: '请输入卡号', trigger: 'blur' },
          ]
        }
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
      this.form.id = this.info.id;
      this.form.card = this.info.card;
      this.form.status = this.info.status;
      this.form.type = this.info.type;
      this.form.count_all = this.info.count_all;
      this.form.count_sold = this.info.count_sold;
    },
    methods: {
      handleSubmit () {
        this.$refs['form'].validate(valid => {
          if (!valid) return;
          this.loading = true;

          card_edit(this.form).then(() => {
            this.loading = false;
            this.$notify({
              title: '提示',
              message: '编辑卡密成功',
              type: 'success', duration: 3000
            });
            this.handleClose(true);
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
