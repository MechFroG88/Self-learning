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
import { getAllLessons } from '@/api/lesson';
import { mapMutations } from 'vuex';

export default {
  data: () => ({
    logout_load: false,
  }),
  mounted() {
    getAllLessons().then(({data}) => {
      this.setLessons(data);
    })
  },
  methods: {
    ...mapMutations('admin_lessons', {
      setLessons: 'SET_LESSONS',
      resetLessons: 'RESET_LESSONS'
    }),
    ...mapMutations('user', {
      logoutUser: 'logout'
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
}
</script>

<style lang="scss">

</style>