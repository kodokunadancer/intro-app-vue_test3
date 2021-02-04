<template>
  <div class="message">
    <!-- サクセスメッセージ -->
    <div class="success-messages" v-show="successMessage">
      {{ successMessage }}
    </div>
    <!-- エラーメッセージ -->
    <div class="error-messages" v-show="errorMessage">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script>
  import { mapState } from 'vuex'

  export default {
    computed: {
      // メッセージストアに値が入っているかチェック
      ...mapState({
        successMessage: state => state.message.successContent,
        errorMessage: state => state.message.errorContent
      })
    }
  }
</script>
