<template>
  <div id="_admin">
    <header class="navbar">
      <section class="navbar-section">
        <router-link :to="{ name: 'adminSearch' }"
        :class="{'active': $route.name == 'adminSearch'}">
          查询名单
        </router-link>
        <router-link :to="{ name: 'adminEditLesson' }"
        :class="{'active': $route.name == 'adminEditLesson'}">
          更改活动
        </router-link>
        <router-link :to="{ name: 'adminEditUser' }"
        :class="{'active': $route.name == 'adminEditUser'}">
          学生列表
        </router-link>
        <router-link :to="{ name: 'adminEditForced' }"
        :class="{'active': $route.name == 'adminEditForced'}">
          保留活动
        </router-link>
      </section>
      <section class="navbar-section">
        <div class="btn btn-link mt-2" 
        :class="{'loading': logout_load}"
        @click="logout">
          登出 <i class="feather icon-log-out"></i>
        </div>
      </section>
    </header>
    <div class="body">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
import { userLogout } from '@/api/user';
import { getAllUsers } from '@/api/user';
import { getAllLessons } from '@/api/lesson';
import { mapMutations, mapState } from 'vuex';

export default {
  data: () => ({
    logout_load: false,
  }),
  mounted() {
    getAllLessons().then(({data}) => { this.setLessons(data); });
    getAllUsers()  .then(({data}) => { this.setUsers(data); });

    // if (window.innerWidth < 400) this.smallScreen = true;
    // window.addEventListener('resize', () => {
    //   if (window.innerWidth < 400) this.smallScreen = true;
    //   else this.smallScreen = false;
    // })
  },
  methods: {
    ...mapMutations('admin_data', {
      setLessons: 'SET_LESSONS',
      resetLessons: 'RESET_LESSONS',
      setUsers: 'SET_USERS',
      resetUsers: 'RESET_USERS'
    }),
    ...mapMutations('user', {
      logoutUser: 'LOGOUT'
    }),
    logout() {
      this.logout_load = true;
      userLogout().then((data) => {
        if (data.status == 200) {
          this.logoutUser();
          this.resetLessons();
          this.$router.push('/');
        }
      }).finally(() => this.logout_load = false)
    }
  },
  computed: {
    ...mapState('user', {
      user: 'user'
    })
  }
}
</script>

<style lang="scss">

</style>