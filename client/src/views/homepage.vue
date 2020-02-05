<template>
  <div id="_homepage" :class="{'loading loading-lg': isPageLoading}">
    <header-layout 
    :cn_name="user.cn_name"
    :id="user.id"></header-layout>
    <div class="body columns">
      <div class="sidebar col-2 col-xs-3">
        <div class="period-counter">
          <h6 style="text-align: center; margin-bottom: 1rem;">节数</h6>
          <div class="cell" v-for="i in 7" :key="i" 
          :class="{'active': actives[i-1]}">
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
        <div class="accordion" v-for="(section, ind) in titles" :key="section">
          <input type="radio" :id="section" name="accordion-checkbox" hidden>
          <label class="accordion-header" :for="section">
            <div class="accordion-header-section">
              <i class="icon icon-arrow-right mr-1"></i>
              {{ section }}
            </div>
            <transition name="fade">
              <small class="label label-rounded label-primary"
              v-if="id[ind]">已选：{{ name[ind] }}</small>
            </transition>
          </label>
          <div class="accordion-body">
            <div class="text-gray ml-2 mt-2 text-bold text-italic text-large" v-if="lessons[ind].length == 0">
              你的年级于此时间段无活动
            </div>
            <card
            v-for="lesson in lessons[ind]" :key="lesson.id"
            :title="lesson.name"
            :initials="lesson.subject.substr(0,1)"
            :pax="lesson.limit"
            :num="lesson.current"
            :classroom="lesson.location"
            bg-color="#ffdf76"
            :active="id[ind] == lesson.id"
            class="c-hand"
            @clicked="choose(ind, lesson.id, lesson.name)"></card>
          </div>
        </div>
        <div class="btn btn-lg btn-secondary submit" :class="{'loading': isSubmitLoading}"
        @click="submit" disabled>
          提交 <i class="feather icon-arrow-right"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
import { userLogout, getUser, submitUser } from '@/api/user';
import { getAllLessons } from '@/api/lesson';

import data from '@/api/mock/lesson.json';

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
      console.log(this.user)
      this.isPageLoading = false;
      // get current year of user
      this.year = (new Date().getFullYear() % 100) - parseInt(this.user.id.toString().substr(0, 2)) + 1;
      this.year = 4
      // getAllLessons().then(({data}) => {
      //   // console.log(data)

        // find lessons valid for user's year and sort lessons according to period sessions
        let lessonArr = this.data.filter(el => el.year.indexOf(this.year) != -1)
        lessonArr.forEach(el => {
          for (let i = 0; i < 4; i++) {
            if (JSON.stringify(el.period.sort()) == JSON.stringify(this.sessions[i])) this.lessons[i].push(el)
          }
        })
        // set initially chosen (haven't submit) lessons and progress bar
        this.id.forEach((el, ind) => {
          if (el) this.sessions[ind].forEach(elem => this.actives[elem-1] = true);
        })
      
      // })
    })
  },
  data: () => ({
    isPageLoading: true,
    isSubmitLoading: false,
    titles: ['第 1-3 节', '第 4-5 节', '第 6-7 节', '第 4-7 节'],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7]],
    actives: new Array(7).fill(false),
    locked: new Array(4).fill(false),
    user: {},
    lessons: [[], [], [], []],
    year: 0,
    logoutLoad: false,
    data
  }),
  methods: {
    ...mapMutations('lessons', {
      select: 'SELECT_LESSON',
      reset: 'RESET'
    }),
    logout() {
      this.logoutLoad = true;
      userLogout().then((data) => {
        if (data.status == 200) {
          this.reset();
          this.$router.push('/');
        }
      }).finally(() => this.logoutLoad = false)
    },
    choose(ind, id, name) {
      if (id == this.id[ind]) {
        // deactivate card and progress bar
        this.sessions[ind].forEach(el => this.actives[el-1] = false);
        this.select({ind, id: 0, name: ""});
      } else {
        // activate card and progress bar
        this.sessions[ind].forEach(el => this.actives[el-1] = true);
        this.select({ind, id, name})
      }
      // update accordion and cards
      this.$forceUpdate();
    },
    submit() {
      this.isSubmitLoading = true;
      submitUser({ lessons: this.id.filter(el => el != 0) })
        .then((data) => {
          console.log(data)
        }).finally(() => this.isSubmitLoading = false)
    }
  },
  computed: {
    ...mapState('lessons', {
      id: 'selected_lesson_id',
      name: 'selected_lesson_name'
    })
  },
}
</script>

<style lang="scss">

</style>