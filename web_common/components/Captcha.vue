<template>
  <div v-if="mode === 'input'">
    <el-input v-if="driver === 'code'"
              ref="code"
              type="text"
              class="captcha-code-input"
              v-model="code.input"
              @input="handleValidate"
              placeholder="请输入验证码">
      <img slot="append" alt="captcha" class="captcha-pulse" :class="{'loading': loading}" :src="code.img"
           title="更换验证码" @click="refresh"/>
    </el-input>
    <div v-else-if="driver === 'geetest'">
      <div :id="geetest.elementId" class="captcha-geetest-container captcha-pulse" :class="{'loading': loading}"></div>
      <button :id="geetest.bindId" style="display: none" @click.prevent="handleValidate"></button>
    </div>
    <div v-else>
      <h1>UNKNOWN CAPTCHA DRIVER</h1>
    </div>
  </div>
  <div v-else>
    <!-- popup 弹窗 -->

    <div v-show="popupDialogVisible" class="captcha-popup-background"></div>
    <div v-show="popupDialogVisible" class="captcha-popup-dialog">
      <div class="captcha-popup-dialog__body">
        <el-input v-if="driver === 'code'"
                  ref="code"
                  type="text"
                  class="captcha-code-input"
                  v-model="code.input"
                  @input="handleValidate"
                  placeholder="请输入验证码">
          <img slot="append" alt="captcha" class="captcha-pulse" :class="{'loading': loading}" :src="code.img"
               title="更换验证码" @click="refresh"/>
        </el-input>
        <div v-else-if="driver === 'geetest'">
          <div :id="geetest.elementId" class="captcha-geetest-container captcha-pulse"
               :class="{'loading': loading}"></div>
          <button :id="geetest.bindId" style="display: none" @click.prevent="handleValidate"></button>
        </div>
        <div v-else>
          <h1>UNKNOWN CAPTCHA DRIVER</h1>
        </div>

        <div class="captcha-popup-dialog__action">
          <el-button @click="handlePopupCancel" :disabled="loading">取消</el-button>
          <el-button v-if="driver === 'code'" style="margin-left: 8px" @click="handlePopupSubmit" :disabled="loading">
            确认
          </el-button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import '~/utils/gt'
  import { captcha } from '~/api/captcha'

  export default {
    name: 'Captcha',
    props: {
      driver: {
        type: String,
      },
      mode: {
        type: String,
        default: 'input' // input or popup
      },
      config: {}
    },
    data() {
      return {
        loading: false,
        code: {
          img: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAEXRFWHRTb2Z0d2FyZQBTbmlwYXN0ZV0Xzt0AAAAMSURBVAiZYzh9+jQABMYCYqKpOacAAAAASUVORK5CYII=',
          input: '',
          key: ''
        },
        geetest: {
          elementId: 'gee-embed-',
          bindId: 'gee-btn-',
          config: window.config.captcha.config,
          validate: null
        },
        popupDialogVisible: false,
        value: null
      }
    },
    beforeMount() {
      if (this.driver === 'code') {

      } else if (this.driver === 'geetest') {
        this.geetest.elementId = 'gee-embed-' + parseInt(Math.random() * 99999);
        this.geetest.bindId = 'gee-btn-' + parseInt(Math.random() * 99999);
      }
    },
    methods: {
      // 刷新验证码
      async refresh() {
        this.loading = true;
        try {
          await this.refresh_internal();
        } catch (e) {
          throw e
        } finally {
          this.loading = false;
        }
      },
      async refresh_internal() {
        if (this.driver === 'code') {
          this.code.input = '';
          const code = await this.$http.get(`//${window.API_HOST}/captcha/api?t=${Math.random()}`);
          this.code.img = code.img;
          this.code.key = code.key;
          this.focus();
        } else if (this.driver === 'geetest') {

          this.geetest.validate = null;
          const oldElement = document.getElementById(this.geetest.elementId);
          if (oldElement) {
            oldElement.innerHTML = '';
          }
          

          this.geetest.config = await captcha();

          
          
          
          

          await new Promise((resolve, reject) => {
            try {
              const timeoutHandler = setTimeout(() => {
                if (this.loading) {
                  console.log(`verifyCode-${this.geetest.elementId} - Geetest timeout`);
                  const oldElement = document.getElementById(this.geetest.elementId);
                  if (oldElement) {
                    oldElement.innerHTML = '';
                  }
                  reject(new Error('加载验证码超时'));
                }
              }, 5000);
              window.initGeetest({
                gt: this.geetest.config.gt,
                challenge: this.geetest.config.challenge,
                product: 'popup', // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                offline: !this.geetest.config.success // 表示用户后台检测极验服务器是否宕机，与SDK配合，用户一般不需要关注
              }, captchaObj => {
                captchaObj.onReady(() => {
                  
                  clearTimeout(timeoutHandler);
                  resolve();
                });
                captchaObj.onError(() => {
                  // 出错啦，可以提醒用户稍后进行重试
                  clearTimeout(timeoutHandler);
                  reject(new Error('加载验证码超时'));
                });
                captchaObj.onSuccess(() => {
                  // 验证成功
                  this.geetest.validate = captchaObj.getValidate();
                  this.handleValidate();
                  this.handlePopupSubmit();
                });

                if (!window['errors']) window['errors'] = []; // 这里极验自身有bug, 触发错误的时候调用errors 但是 errors 未定义

                if (document.getElementById(this.geetest.elementId)) {
                  // 这里加个判断是为了防止路由view已经销毁, 但是极验的调用正好加载, 这时候embed-captcha 已经不存在了, 导致错误
                  captchaObj.appendTo('#' + this.geetest.elementId);
                  if (captchaObj.bindOn) captchaObj.bindOn('#' + this.geetest.bindId);
                  // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
                }

                this.geetest.obj = captchaObj;
              });
            } catch (e) {
              reject(new Error('验证码初始化失败: ' + e));
              
            }
          });
        } else if (this.driver === 'recaptcha') {
          // xxx
          console.error('captcha.recaptcha: unimplemented')
        } else {
          throw new Error('unimplemented');
        }
      },
      // 前端提前验证一下是否已经通过
      verify() {
        return new Promise((resolve, reject) => {
          if (this.driver === 'code') {
            if (this.code.input && this.code.input.length) {
              resolve();
            } else {
              if (this.mode === 'input') {
                reject(new Error('请输入验证码'));
              } else {
                this.popupDialogVisible = true;
                this.$nextTick().then(() => {
                  this.focus();
                })
                this._verify_promise = {
                  resolve,
                  reject
                }
              }
            }
          } else if (this.driver === 'geetest') {
            if (this.geetest.validate) {
              resolve();
            } else {
              if (this.mode === 'input') {
                reject(new Error('请先完成验证'));
              } else {
                this.popupDialogVisible = true;
                this._verify_promise = {
                  resolve,
                  reject
                }
              }
            }
          } else if (this.driver === 'recaptcha') {
            reject(new Error('unimplemented'));
          }
        });
      },
      focus() {
        if (this.driver === 'code') {
          if (this.$refs.code) this.$refs.code.focus();
        }
      },
      handleValidate() {
        this.value = 'unimplemented';
        if (this.driver === 'code') {
          this.value = {
            code: this.code.input,
            key: this.code.key,
          }
        } else if (this.driver === 'geetest') {
          this.value = {
            a: this.geetest.config.key,
            b: this.geetest.validate.geetest_challenge,
            c: this.geetest.validate.geetest_validate,
            d: this.geetest.validate.geetest_seccode,
          }
        } else if (this.driver === 'recaptcha') {

        }
        this.$emit('input', this.value);
      },
      onPopupDialogClosed() {
        this.handlePopupCancel();
      },
      handlePopupCancel() {
        if (this._verify_promise) {
          this.popupDialogVisible = false;
          this._verify_promise.reject(new Error('cancel'))
          delete this._verify_promise;
        }
      },
      handlePopupSubmit() {
        if (this._verify_promise) {
          this.popupDialogVisible = false;
          this._verify_promise.resolve();
          delete this._verify_promise;
        }
      }
    }
  }
</script>

<style lang="scss">
  .captcha-code-input {
    .el-input-group__append {
      padding: 0 4px;
      border: 0;
      background: none;
      cursor: pointer;

      img {
        display: block;
        width: 120px;
        height: 38px;
      }
    }
  }

  @keyframes shellPulse {
    0% {
      opacity: 1
    }

    50% {
      opacity: .3
    }

    100% {
      opacity: 1
    }
  }

  .captcha-geetest-container {
    height: 42px;
    background: #f4f4f4;
  }

  .captcha-pulse {
    &.loading {
      animation: shellPulse 1s infinite;
    }
  }

  .captcha-popup-background {
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    opacity: .5;
    background: #000;
    z-index: 99999999; // bigger than MuseUI or ElementUI
  }

  .captcha-popup-dialog {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    overflow: auto;
    margin: 0;
    z-index: 100000000; // bigger than MuseUI or ElementUI

    display: flex;
    justify-content: center;
    align-items: center;

    .captcha-popup-dialog__body {
      padding: 24px;
      display: block;
      background-color: #fff;
      border-radius: 2px;
      border: 1px solid #ebeef5;
      width: 90% !important;
      max-width: 357px !important;
      box-shadow: 0 5px 5px -3px rgba(0, 0, 0, .2), 0 8px 10px 1px rgba(0, 0, 0, .14), 0 3px 14px 2px rgba(0, 0, 0, .12);


      .captcha-popup-dialog__action {
        text-align: right;
        display: block;
        margin: 16px 0 0;
      }
    }
  }
</style>