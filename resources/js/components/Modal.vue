<template>
  <!-- モーダル -->
  <div>
    <div class="modal micromodal-slide" :class="[isOpen ? 'open-modal' : 'close-modal']" id="modal">
      <!-- この div がオーバーレイになる -->
      <div class="modal__overlay" tabindex="-1" id="modal-window">
        <div
          class="modal__container"
          role="dialog"
          aria-modal="true"
          aria-labelledby="modal-title"
          aria-describedby="modal-content"
        >
          <header class="modal__header">
            <h2 class="modal__title" id="modal-title">確認</h2>
          </header>
          <!-- モーダルの内容 -->
          <main class="modal__content" id="modal-content">
            <p>{{ contentText }}</p>
          </main>
          <!-- モーダルのボタン -->
          <footer class="modal__footer">
            <button class="modal__btn" @click="resetModalContent">キャンセル</button>
            <button class="modal__btn modal__btn-primary" @click="confirm" v-show="!checkModalContent">{{ buttonText }}</button>
            <button
              class="modal__btn modal__btn-primary"
              @click="confirm"
              v-show="checkModalContent"
              v-clipboard:copy="message"
              v-clipboard:success="onCopy"
              v-clipboard:error="onError"
            >
              {{ buttonText }}
            </button>
          </footer>
        </div>
      </div>
    </div>
 </div>
</template>


<script>
import { mapState } from 'vuex'

export default {

  data() {
    return {
      message: ''
    }
  },
  computed: mapState({
    isOpen: state => state.modal.modalStatus,
    contentText: state => state.modal.contentText,
    buttonText: state => state.modal.buttonText,
    modalContent: state => state.modal.modalContent,
    checkModalContent: state => state.modal.modalContent === 'copy',
    checkSuccessContent: state => state.message.successContent,
    checkErrorContent: state => state.message.errorContent
  }),
  methods: {
    resetModalContent() {
      this.$store.commit('modal/setModal', {
        modalStatus: false,
        contentText: '',
        buttonText: '',
        modalContent: '',
      })
    },
    //モーダルのボタンをクリックしたときに発火
    confirm() {
      if( this.modalContent === 'exit'){
        this.$store.commit('modal/setExitGroup', true)
        this.resetModalContent()
      }
      else if ( this.modalContent === 'selectExit') {
        this.$store.commit('modal/setSelectExitGroup', true)
        this.resetModalContent()
      }
      else if ( this.modalContent === 'naviExit') {
        this.$store.commit('modal/setNaviExitGroup', true)
        this.resetModalContent()
      }
      else if( this.modalContent === 'delete') {
        this.$store.commit('modal/setDeleteGroup', true)
        this.resetModalContent()
      }
      else if ( this.modalContent === 'forceExit') {
        this.$store.commit('modal/setForceExit', true)
        this.resetModalContent()
      }
      else if (this.modalContent === 'copy') {
        this.message = this.contentText
      }
    },
    onCopy() {
      this.$store.commit('message/setSuccessContent', {
        successContent: "クリップボードにコピーしました",
        timeout: 6000
      })
      this.resetModalContent()
    },
    onError() {
      this.$store.commit('message/setErrorContent', {
        errorContent: "クリップボードのコピーに失敗しました",
        timeout: 6000
      })
    }
  },
  // メッセージが表示されたら、一番上へスクロールアップする
  watch: {
    checkSuccessContent: {
      handler() {
        window.scrollTo({
          top: 0,
          behavior: "smooth"
        })
      }
    },
    checkErrorContent: {
      handler() {
        window.scrollTo({
          top: 0,
          behavior: "smooth"
        })
      }
    },
  }
}
</script>
