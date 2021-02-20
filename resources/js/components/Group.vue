<template>
  <div class="group">
    <!-- メニューアイコン -->
    <div class="group-menu">
      <button class="group-menu__btn" @click="toggleMenu(index)" v-click-outside="hide"><i class="fas fa-ellipsis-h group-menu__icon"></i></button>
    </div>
    <!-- ドロップダウンリスト -->
    <div class="dropdown">
      <ul class="menu-list" :class="{'is-open': dropdown}">
        <li class="menu-item">
          <button class="menu-btn" @click="openPasswordModal">パスワードの確認</button>
        </li>
        <li class="menu-item" v-show="id === group.author_id">
          <button class="menu-btn">
            <RouterLink
              :to="`/mypage/${ id }/groups/${ group.id }/edit`"
              class="group__edit-link"
            >
            編集する
            </RouterLink>
          </button>
        </li>
        <li class="menu-item">
          <button class="menu-btn" @click="openExitModal">退会する</button>
        </li>
        <li class="menu-item" v-show="id === group.author_id">
          <button class="menu-btn" @click="openDeleteModal">削除する</button>
        </li>
      </ul>
    </div>
    <!-- グループパネル -->
    <RouterLink
      :to = "`/mypage/${ id }/groups/${ group.id }`"
      :class="{ 'not-link' : dropdown }"
      class="group-panel"
    >
      <div class="group-panel__info">
        <!-- グループ画像 -->
        <div class="group-panel__photo" v-if="group.photo">
          <img class="group-img" :src="group.photo.url">
        </div>
        <div class="group-panel__photo" v-else>
          <img src="https://introduction-app.s3-ap-northeast-1.amazonaws.com/groups/group_img.png">
        </div>
        <!-- グループ名 -->
        <div class="group-panel__name">{{ group.name }}</div>
      </div>
    </RouterLink>
  </div>
</template>


<script>

import { OK } from '../util.js'
import ClickOutside from 'vue-click-outside'
export default {

  props: {
    id: {
      type: Number,
      required: true
    },
    group: {
      type: Object,
      required: true
    },
    index: {
      type: Number,
      required: true
    },
    groups: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      dropdown: false,
      clickedGroupNumber: null
    }
  },
  methods: {
    //クリックされたグループのインデックス番号を引数に受け取る
    toggleMenu(index) {
      //ドロップダウンの開閉
      this.dropdown =! this.dropdown
      if(this.dropdown) {
        this.clickedGroupNumber = index
      }
      else {
        this.clickedGroupNumber = null
      }
    },
    //v-click-outsideの機能で、メニューアイコン以外をクリックしたときに発火する
    hide() {
      this.dropdown = false
      //タイミングを送らせなければ、watchの処理が進まない
      setTimeout(this.clear ,500)
    },
    clear() {
      this.clickedGroupNumber = null
    },
    //モーダルに表示する内容をモーダルストアへ送る（以下２つ同じ）
    openPasswordModal() {
      this.$store.commit('modal/setModal', {
        modalStatus: true,
        contentText: `${ this.group.password}`,
        buttonText: 'コピーする',
        modalContent: 'copy'
      })
    },
    openExitModal() {
      this.$store.commit('modal/setModal', {
        modalStatus: true,
        contentText: `本当に「${ this.group.name}」から退会しますか？`,
        buttonText: '退会する',
        modalContent: 'exit'
      })
    },
    openDeleteModal() {
      this.$store.commit('modal/setModal', {
        modalStatus: true,
        contentText: `本当に「${ this.group.name}」を削除しますか？`,
        buttonText: '削除する',
        modalContent: 'delete'
      })
    },
    // ページのリロード
    reload() {
      this.$router.go({path: this.$router.currentRoute.path, force: true })
    }
  },
  computed: {
    //選択されたグループが、このGroupコンポーネントであるかチェック
    checkClickedGroup() {
      return this.clickedGroupNumber === this.index
    },
    //モーダルにて退会ボタンをクリックしたかチェック（モーダルストアにそのフラグがステートでセットされている）
    exitGroup() {
      return this.$store.state.modal.exitGroup
    },
    //モーダルにて削除ボタンをクリックしたかチェック
    deleteGroup() {
      return this.$store.state.modal.deleteGroup
    }
  },
  watch: {
    //モーダルストアのexitGroupフラグを監視
    exitGroup: {
      async handler(val) {
        //モーダルにて退会ボタンをクリックし,退会しようとしているグループがこのGroupコンポーネントである場合
        if(val && this.checkClickedGroup) {
          const response = await axios.get(`/api/mypage/${ this.id }/groups/${ this.groups[this.clickedGroupNumber].id }/exit`)
          if(response.status !== OK) {
            this.$store.commit('message/setErrorContent', {
              errorContent: "グループの退会に失敗しました",
              timeout: 6000
            })
            this.$store.commit('error/setCode',response.status)
          }
          this.reload()
          this.$store.commit('message/setSuccessContent', {
            successContent: `「${ this.group.name }」から退会しました`,
            timeout: 6000
          })
        }
      }
    },
    //以下同じ
    deleteGroup: {
      async handler(val) {
        if(val && this.checkClickedGroup) {
          const response = await axios.get(`/api/mypage/${ this.id }/groups/${ this.groups[this.clickedGroupNumber].id }/delete`)
          if(response.status !== OK) {
            this.$store.commit('message/setErrorContent', {
              errorContent: "グループの削除に失敗しました",
              timeout: 6000
            })
            this.$store.commit('error/setCode',response.status)
          }
          this.reload()
          this.$store.commit('message/setSuccessContent', {
            successContent: `「${ this.group.name }」の削除を完了しました`,
            timeout: 6000
          })
        }
      }
    },
    //ページが切り替わった場合、処理が開始してしまうフラグをfalseにしておく
    $route() {
      this.$store.commit('modal/setExitGroup', false)
      this.$store.commit('modal/setDeleteGroup', false)
      immediate: true
    }
  },
  //popupItemを使用して、イベントの外側をクリックしないようにする
  mounted () {
    this.popupItem = this.$el
  },
  //directivesオプションを使用することにより、ローカルディレクティブに登録されるため、機能を使うことができる
  directives: {
    ClickOutside
  }
}

</script>
