<template>
  <div id="_homepage">
    <header-layout 
    :cn_name="user.cn_name"
    :id="user.id"></header-layout>
    <div class="body columns">
      <div class="sidebar col-2 col-xs-3">
        <div class="period-counter">
          <h6 style="text-align: center; margin-bottom: 1rem;">节数</h6>
          <div class="cell" v-for="i in 7" :key="i" 
          :class="{'active': i < 4}">
            <div class="period">{{i}}</div>
            <div class="progress-bar"></div>
          </div>
        </div>
        <div class="logout" @click="logout"
        :class="{'loading': logoutLoad}">
          <i class="feather icon-log-out"></i>登出
        </div>
      </div>
      <div class="content column col-10 col-xs-9">
        <div class="accordion" v-for="i in titles" :key="i">
          <input type="radio" :id="i" name="accordion-checkbox" hidden>
          <label class="accordion-header" :for="i">
            <div class="accordion-header-section">
              <i class="icon icon-arrow-right mr-1"></i>
              {{i}}
            </div>
            <small class="label label-rounded label-primary">已选：活动1</small>
          </label>
          <div class="accordion-body">
            <card
            title="活动标题"
            initials="华"
            :pax="800"
            :num="351"
            classroom="4A Ren"
            bg-color="#ffdf76"
            active></card>
            <card
            title="活动标题"
            initials="华"
            :pax="800"
            :num="551"
            classroom="4A Ren"
            bg-color="#ffdf76"></card>
            <card
            title="活动标题"
            initials="华"
            :pax="800"
            :num="351"
            classroom="4A Ren"
            bg-color="#ffdf76"></card>
            <card
            title="活动标题"
            initials="华"
            :pax="800"
            :num="751"
            classroom="4A Ren"
            bg-color="#ffdf76"></card>
          </div>
        </div>
        <div class="btn btn-lg btn-secondary submit">
          提交 <i class="feather icon-arrow-right"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { userLogout, getUser } from '@/api/user';
import { getAllLessons } from '@/api/lesson';

import card from '@/components/card';
import headerLayout from '@/layout/header';

export default {
  components: {
    card,
    headerLayout,
  },
  mounted() {
    getUser().then(({data}) => {
      this.user = data;
    })
    getAllLessons().then(({data}) => {
      console.log(data)
    })
  },
  data: () => ({
    titles: ['第 1-3 节', '第 4-5 节', '第 6-7 节', '第 4-7 节'],
    user: {},
    logoutLoad: false,
  }),
  methods: {
    logout() {
      this.logoutLoad = true;
      userLogout().then((data) => {
        if (data.status == 200) {
          this.$router.push('/');
        }
      }).finally(() => this.logoutLoad = false)
    }
  }
}
</script>

<style lang="scss">

</style>