// ページコンポーネントの切り替えを指揮する
import Vue from 'vue'
import VueRouter from 'vue-router'
// ここでstoreのgettersを使えるようにするため
import store from './store'

// ページコンポーネントをインポートする
import Home from './pages/Home.vue'
import Login from './pages/Login.vue'
import CreateProfile from './pages/profiles/CreateProfile.vue'
import ShowMyProfile from './pages/profiles/ShowMyProfile.vue'
import EditProfile from './pages/profiles/EditProfile.vue'
import GroupList from './pages/groups/GroupList.vue'
import GroupDetail from './pages/groups/GroupDetail.vue'
import CreateGroup from './pages/groups/CreateGroup.vue'
import EditGroup from './pages/groups/EditGroup.vue'
import ShowProfile from './pages/profiles/ShowProfile.vue'
import ReserchGroup from './pages/groups/ReserchGroup.vue'
import AuthenticationError from './pages/errors/Authentication.vue'
import PermissionError from './pages/errors/Permission.vue'
import TokenError from './pages/errors/Token.vue'
import SystemError from './pages/errors/System.vue'
import NotFound from './pages/errors/NotFound.vue'

Vue.use(VueRouter)
const routes = [
  {
    path: '/',
    component: Home
  },
  {
    path: '/login',
    component: Login,
    name: 'login',
    beforeEnter(to, from, next) {
      if(store.getters['auth/check']) {
        next(`/mypage/${store.getters['auth/userid']}/groups`)
      }
      else{
        next()
      }
    }
  },
  // プロフィール作成ページ
  {
    path: '/profile/create',
    component: CreateProfile,
    name: 'CreateProfile',
    beforeEnter(to, from, next) {
      if(store.getters['profile/check']) {
        next(`/mypage/${store.getters['auth/userid']}/groups`)
      }
      else{
        next()
      }
    }
  },
  //マイプロフィールページ
  {
    path: '/mypage/:id/myprofile',
    name: 'ShowMyProfile',
    component: ShowMyProfile,
    props: route => ({
      id: Number(route.params.id)
    }),
  },
  //マイプロフィール編集ページ
  {
    path: '/mypage/:id/myprofile/edit',
    name: 'EditProfile',
    component: EditProfile,
    props: route => ({
      id: Number(route.params.id)
    }),
  },
  // グループ一覧ページ
  {
    path: '/mypage/:id/groups',
    name: 'GroupList',
    component: GroupList,
    props: route => ({
      id: Number(route.params.id)
    }),
  },
  // グループ作成ページ
  {
    path: '/mypage/:id/groups/create',
    component: CreateGroup,
    name: 'CreateGroup',
    props: route => ({
      id: Number(route.params.id)
    })
  },
  // グループ検索ページ
  {
    path: '/mypage/:id/groups/reserch',
    component: ReserchGroup,
    name: 'ReserchGroup',
    props: route => ({
      id: Number(route.params.id)
    })
  },
  // グループ詳細ページ
  {
    path: '/mypage/:id/groups/:group',
    name: 'GroupDetail',
    component: GroupDetail,
    props: route => ({
      id: Number(route.params.id),
      group: Number(route.params.group)
    })
  },
  //グループ編集ページ
  {
    path: '/mypage/:id/groups/:group/edit',
    name: 'EditGroup',
    component: EditGroup,
    props: route => ({
      id: Number(route.params.id),
      group: Number(route.params.group)
    })
  },
  //プロフィール詳細
  {
    path: '/mypage/:id/groups/:group/profiles/:profile',
    name: 'ShowProfile',
    component: ShowProfile,
    props: route => ({
      id: Number(route.params.id),
      group: Number(route.params.group),
      profile: Number(route.params.profile)
    })
  },
  // 認証エラー
  {
    path: '/401',
    component: AuthenticationError,
    name: '401'
  },
  //権限エラー
  {
    path: '/403',
    component: PermissionError,
    name: '403'
  },
  //トークンエラー
  {
    path: '/419',
    component: TokenError,
    name: '419'
  },
  //システムエラー
  {
    path: '/500',
    component: SystemError,
    name: '500'
  },
  //存在しないページへのアクセス
  {
    path: '*',
    component: NotFound
  }
]

const router = new VueRouter({
  mode: 'history',
  scrollBehavior () {
    return { x: 0, y: 0 }
  },
  routes
})

export default router
