<template>
  <div class="edit-group">
    <!-- ローディング -->
    <div class="loading" v-show="loading">
      <Loader>ロード中・・・</Loader>
    </div>
    <div class="edit-group-contets" v-if="!loading">
      <EditGroupForm
      :id="id"
      :group="group"
      :editGroup="editGroup"
      />
    </div>
  </div>
</template>

<script>

import EditGroupForm from '../../components/EditGroupForm.vue'
import { OK } from '../../util.js'
import Loader from '../../components/Loader'

export default {

  components: {
    EditGroupForm,
    Loader
  },
  props: {
    id: {
      type: Number,
      required: true
    },
    group: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      editGroup: null,
      loading: false
    }
  },
  methods: {
    // 編集しようとしているグループの取得
    async getEditGroup() {
      this.loading = true
      const response = await axios.get(`/api/groups/${this.group}`)
      if( response.status !== OK ) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.loading = false
      this.editGroup = response.data
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.getEditGroup()
      },
      immediate: true
    }
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.$store.commit('route/setPrevRoute', from)
    })
  }
}

</script>
