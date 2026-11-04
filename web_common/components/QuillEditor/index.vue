<template>
  <div class="quill-editor" :class="{'quill-editor_disabled': disabled}">
    <div :id="id_toolbar">
      <span class="ql-formats">
        <select class="ql-size" title="字体">
          <option value="small">小号</option>
          <option selected>普通</option>
          <option value="large">大号</option>
          <option value="huge">超大</option>
        </select>
        <button class="ql-bold" title="加粗"></button>
        <button class="ql-italic" title="倾斜"></button>
      </span>
      <span class="ql-formats">
        <select class="ql-color" title="颜色"></select>
        <select class="ql-background" title="背景色"></select>
      </span>
      <span class="ql-formats">
        <button class="ql-list" value="ordered" title="序号列表"></button>
        <button class="ql-list" value="bullet" title="列表"></button>
        <select class="ql-align" title="布局">
          <option selected=""></option>
          <option value="center"></option>
          <option value="right"></option>
          <option value="justify"></option>
        </select>
      </span>
      <span class="ql-formats">
        <button class="ql-image" title="插入图片"></button>
        <button class="ql-link" title="插入链接"></button>
        <button class="ql-qq_group" title="插入QQ群" @click="handleInsertQQGroup">
          <img width="16" :src="require('./qq-group.png')">
        </button>
      </span>
    </div>
    <div :id="id_editor"></div>
    <div v-if="showUpload" @click="showUpload=false" style="z-index: 1010" class="v-modal"></div>
    <div v-if="showUpload" ref="upload_wrapper" @click="onUploadClose" style="z-index: 1011"
         class="el-message-box__wrapper">
      <div class="upload-box">
        <el-radio v-model="upload.type" label="upload">手动上传</el-radio>
        <el-radio v-model="upload.type" label="input">网络图片</el-radio>
        <el-upload v-show="upload.type === 'upload'"
                   drag
                   list-type="picture"
                   :action="upload_url"
                   :multiple="false"
                   :data="{user_id:userInfo_?userInfo_.id:''}"
                   :headers="upload_headers"
                   :limit="1"
                   :with-credentials="false"
                   :on-error="handleUploadError"
                   :on-change="handleUploadChange">
          <i class="el-icon-upload"></i>
          <div class="el-upload__text">将文件拖到此处，或<em>点击上传</em>
            <div style="font-size: 12px">只能上传图片文件，且不超过5MB</div>
          </div>
        </el-upload>
        <el-input v-show="upload.type === 'input'"
                  type="text" v-model="upload.manual" placeholder="请输入图片URL地址">
          <el-button slot="append" type="text" @click="handleUploadInputUrl">确定</el-button>
        </el-input>
      </div>
    </div>
  </div>
</template>

<script>
  import Quill from 'quill'
  import ImageResize from 'quill-image-resize-module'
  import { randomString } from '~/utils/helper';

  Quill.register('modules/imageResize', ImageResize);
  Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('mqqapi');

  export default {
    name: 'QuillEditor',
    props: {
      value: {
        default: ''
      },
      placeholder: {
        default: '请输入内容'
      },
      disabled: {
        default: false
      },
      theme: {
        default: 'snow' // or 'bubble'
      },
      userInfo: {}, // admin 上传图片的时候要记录用户ID
      upload_url: {
        default: window.API_URL + 'file/upload'
      },
    },
    data() {
      const id = randomString(8);
      return {
        userInfo_: null,
        id_editor: 'editor-' + id,
        id_toolbar: 'editor-toolbar-' + id,
        quill_editor: null,
        showUpload: false,
        upload_headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Authorization': this.$store.state.user.token
        },
        upload: {
          type: 'upload',
          manual: ''
        },
      }
    },
    watch: {
      disabled() {
        if (!this.quill_editor) return;
        if (this.disabled) {
          this.quill_editor.disable();
        } else {
          this.quill_editor.enable();
        }
      }
    },
    mounted() {
      if (this.userInfo) {
        this.userInfo_ = this.userInfo;
      } else {
        this.userInfo_ = this.$store.getters.userInfo;
      }
      
      this.quill_editor = new Quill('#' + this.id_editor, {
        modules: {
          toolbar: '#' + this.id_toolbar,
          imageResize: true,
        },
        placeholder: this.placeholder,
        theme: this.theme
      });
      this.quill_editor.on('editor-change', (eventName, ...args) => {
        if (eventName === 'text-change') {
          this.$emit('change', args[0])
          // args[0] will be delta
        } else if (eventName === 'selection-change') {
          // args[0] will be old range
        }
      });
      this.quill_editor.getModule('toolbar').addHandler('image', () => {
        if (this.disabled) return;
        const pos = this.quill_editor.getSelection();
        this.showUpload = true;
        this.upload.manual = '';
        this.uploadSuccess = url => {
          console.log(this.id_editor + ' uploadSuccess', url);
          this.quill_editor.insertEmbed(pos.index, 'image', url, 'user');
        }
      });

      

      this.setValue(this.value);
    },
    beforeDestroy() {
      
      this.quill_editor = null;
    },
    methods: {
      onUploadClose(e) {
        if (e.target === this.$refs['upload_wrapper']) {
          this.showUpload = false;
        }
      },
      handleUploadChange(file, fileList) {
        if (fileList.length > 0 && fileList[0].response) {
          const res = fileList[0].response;
          console.log(res);
          if (res.data) {
            if (!res.data.url) {
              this.showUpload = false;
              return this.$message({ message: res.msg, type: 'error' });
            }
            this.uploadSuccess(res.data.url);
            this.showUpload = false;
          } else {
            return this.$msgbox({ message: res, title: '错误', type: 'error' });
          }
        }
      },
      handleUploadError(err, file, fileList) {
        console.warn('handleUploadError');
        console.warn(err);
        try {
          const msg = JSON.parse(err.message).message;
          if (msg) return this.$message({ message: msg, type: 'error' });
        } catch (e) {
          this.$message({ message: '文件上传失败，请检查文件大小和格式', type: 'error' });
        }
      },
      handleUploadInputUrl() {
        this.upload.manual = this.upload.manual.trim();
        if (!this.upload.manual.startsWith('http://') &&
          !this.upload.manual.startsWith('https://') &&
          !this.upload.manual.startsWith('ftp://')) {
          this.$alert('请输入合法的图片URL地址');
          return;
        }
        this.uploadSuccess(this.upload.manual);
        this.showUpload = false;
      },
      handleInsertQQGroup(event) {
        if (this.disabled) return;
        this.quill_editor.focus();
        const pos = this.quill_editor.getSelection();
        if (!pos) return;
        this.$prompt('请输入加群链接<br>获取方式: <br>手机QQ - 选择QQ群 - 右上角菜单 - 分享群聊 - 复制链接', '插入QQ群加群按钮', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          inputPlaceholder: 'https://jq.qq.com/?_wv=1027&k=xxxx',
          inputPattern: /https?:\/\/.+/,
          inputErrorMessage: '请输入正确的加群链接',
          closeOnClickModal: false,
          dangerouslyUseHTMLString: true,
        }).then(({ value }) => {
          const Delta = Quill.import('delta');
          const delta = new Delta().retain(pos.index)
            .insert({
              'image': '//pub.idqqimg.com/wpa/images/group.png'
            }, {
              alt: value,
              link: value
            });
          this.quill_editor.updateContents(delta, 'user');
        }).finally(() => {
          this.quill_editor.blur();
        });
      },
      /**
       * 这个组件不用VUE的 v-model 主要是由于性能问题
       * @returns {string}
       */
      getValue() {
        return JSON.stringify(this.quill_editor.getContents());
      },
      setValue(val) {
        
        
        if (val && val[0] === '{') {
          try {
            this.quill_editor.setContents(JSON.parse(val));
          } catch (e) {
            this.quill_editor.setText(val || '');
          }
        } else {
          this.quill_editor.setText(val || '');
        }
      }
    }
  }
</script>

<style lang="scss">

  .quill-editor {
    .ql-toolbar {
      line-height: 1 !important;
      padding-bottom: 0 !important;

      & > .ql-formats {
        
      }

      @media only screen and (max-width: 615px) {
        .ql-picker.ql-size {
          width: 64px !important;
        }
      }

      .ql-picker-label::before {
        vertical-align: top;
      }

      .ql-picker.ql-expanded .ql-picker-options {
        z-index: 2;
      }

      svg {
        vertical-align: top;
      }
    }

    .ql-container {
      min-height: 135px;
    }

    .ql-tooltip {
      z-index: 1020;
    }
  }

  .quill-editor_disabled {
    background-color: #f5f7fa;
    border-color: #e4e7ed;
    color: #c0c4cc;
    cursor: not-allowed;

    button, select {
      cursor: not-allowed !important;

      &:hover .ql-stroke {
        stroke: #444 !important;
      }
    }
  }

  .upload-box {
    position: relative;
    display: inline-block;
    vertical-align: middle;
    background-color: #fff;
    border-radius: 4px;
    border: 1px solid #ebeef5;
    min-width: 100px;
    min-height: 100px;

    .el-upload-dragger {
      padding: 0 48px;
      width: auto !important;
    }
  }
</style>
