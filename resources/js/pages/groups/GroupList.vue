<template>
  <div class="group-list">
    <!-- ローディング -->
    <div class="loading" v-show="loading">
      <Loader>ロード中・・・</Loader>
    </div>
    <!-- loadingの前に「!」をつけることで、真偽の判定が逆になる -->
    <div class="group-list-contets" v-if="!loading">
      <div class="tittle">グループ一覧</div>
      <div class="group-wrapper" v-if="groups.length">
        <Group
          v-for="(group,index) in groups"
          :key="group.id"
          :id="id"
          :group="group"
          :index="index"
          :groups="groups"
        />
     </div>
     <div class="no-gorup" v-else>
       <p class="no-group-text">まだグループに所属していません。まずはグループを作成するか検索しましょう！</p>
     </div>
   </div>
  </div>
</template>

<script>
import { OK } from '../../util'
import Group from '../../components/Group.vue'
import Loader from '../../components/Loader.vue'

export default {
  components: {
    Group,
    Loader
  },
  data() {
    return {
      groups: [],
      loading: false
    }
  },
  props: {
    id: {
      type: Number,
      required: true,
    }
  },
  methods: {
    //所属グループの取得
    async fetchGroups() {
      this.loading = true
      const response = await axios.get(`/api/mypage/${ this.id }/groups`)
      if (response.status !== OK) {
        this.$store.commit('error/setCode', response.status)
        return false
      }
      this.loading = false
      this.groups = response.data
      this.$store.commit('group/setCurrentGroup', null)
    }
  },
  watch: {
    $route: {
      async handler() {
        await this.fetchGroups()
      },
      immediate: true
    }
  }
}
</script>
