<template>
  <div v-if="images.length>0">
    <el-button type="text" @click.native="showImgId=0">点击查看</el-button>

    <div v-if="showImgId>-1" class="mfp-bg"></div>
    <div v-if="showImgId>-1"
         class="mfp-wrap mfp-gallery"
         tabindex="-1" style="overflow-x: hidden; overflow-y: auto; height: 100%;">
      <div class="mfp-container mfp-image-holder"
           :class="{'mfp-s-ready':imgLoaded==1,'mfp-loading':imgLoaded==0,'mfp-s-error':imgLoaded==-1}">
        <div class="mfp-content">
          <div class="mfp-figure">
            <button title="Close" type="button" class="mfp-close" @click="showImgId=-1">×</button>
            <figure><img class="mfp-img" @load="imgLoaded=1" @error="imgLoaded=-1"
                         :src="images[showImgId].path"
                         :style="{'max-height': maxHeight}">
              <figcaption>
                <div class="mfp-bottom-bar">
                  <div class="mfp-title">{{images[showImgId].name}}
                    <small>{{images[showImgId].created_at}}</small>
                  </div>
                  <div class="mfp-counter">{{showImgId + 1}} of {{images.length}}</div>
                </div>
              </figcaption>
            </figure>
          </div>
        </div>
        <div class="mfp-preloader">Loading image ...</div>
        <div class="mfp-load-error">Load failed</div>
        <button v-if="0 < showImgId" title="Previous" type="button"
                @click="--showImgId"
                class="mfp-arrow mfp-arrow-left mfp-prevent-close"></button>
        <button v-if="showImgId < images.length - 1" title="Next" type="button"
                @click="++showImgId"
                class="mfp-arrow mfp-arrow-right mfp-prevent-close"></button>
      </div>
    </div>
  </div>

  <div v-else>无</div>
</template>

<script>
  export default {
    props: {
      images: {
        type: Array
      }
    },
    data () {
      return {
        showImgId: -1,
        imgLoaded: 0, // 0=loading 1=success -1=failed
        maxHeight: window.innerHeight * 0.9 + 'px'
      }
    },
    mounted () {
      this._on_app_resize = () => {
        this.maxHeight = window.innerHeight * 0.9 + 'px';
      };
      window.bus.$on('app-resize', this._on_app_resize);
    },
    beforeDestroy () {
      window.bus.$off('app-resize', this._on_app_resize);
    },
    watch: {
      showImgId () {
        this.imgLoaded = 0;
      }
    }
  }
</script>

<style lang="scss">
  @import "popup.scss";
</style>
