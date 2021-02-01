<!-- index.balde.phpの<div id="app"></div>の中にぶち込まれる内容 -->
<template>
  <div class="container">
    <header>
      <Navbar />
    </header>
    <main>
      <div class="contents">
        <Message />
        <Modal />
        <Select />
        <RouterView />
      </div>
    </main>
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue'
import Message from './components/Message.vue'
import Modal from './components/Modal.vue'
import Select from './components/Select.vue'
import { NOT_FOUND, UNAUTHORIZED, INTERNAL_SERVER_ERROR, TOKEN_ERROR, FORBIDDEN } from './util'
export default {
  components: {
    Navbar,
    Message,
    Modal,
    Select
  },
  computed: {
    errorCode () {
      return this.$store.state.error.code
    }
  },
  watch: {
    errorCode: {
      // handlerの引数は、状態の変更後の値
      handler (val) {
        //システムエラー
        if (val === INTERNAL_SERVER_ERROR) {
          this.$router.push('/500')
        }
        //認証エラー
        else if (val === UNAUTHORIZED) {
          this.$router.push('/refresh-token')
          this.$store.commit('auth/setUser', null)
          this.$router.push('/401')
        }
        //権限エラー
        else if (val === FORBIDDEN ) {
          this.$router.push('/403')
        }
        //トークンエラー
        else if (val === TOKEN_ERROR) {
          this.$router.push('/refresh-token')
          this.$store.commit('auth/setUser', null)
          this.$router.push('/419')
        }
        //存在しないページのエラー
        else if (val === NOT_FOUND) {
          this.$router.push('/not-found')
        }
      },
      immediate: true
    },
    $route () {
      this.$store.commit('error/setCode', null)
    }
  }
}
</script>
