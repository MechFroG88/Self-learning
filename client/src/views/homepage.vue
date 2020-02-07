<template>
  <div id="_homepage" :class="{'loading loading-lg': isPageLoading}">
    <header-layout 
    :cn_name="user.cn_name"
    :id="user.id"></header-layout>
    <div class="body columns">
      <div class="sidebar col-2 col-xs-3">
        <div class="period-counter" v-if="!isPageLoading">
          <h6 style="text-align: center; margin-bottom: 1rem;">节数</h6>
          <div class="cell" v-for="i in 7" :key="i" 
          :class="{
            'active': actives[i-1],
            'chosen': chosen[i-1],
          }">
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
        <div class="accordion" :class="{'disabled': dis[ind]}"
        v-for="(section, ind) in titles" :key="section"
        v-if="!isPageLoading">
          <input type="radio" :id="section" name="accordion-checkbox" hidden :disabled="dis[ind] || invalidSelect(ind)">
          <label class="accordion-header" :for="section">
            <div class="accordion-header-section" :class="{'tooltip': dis[ind] || invalidSelect(ind)}"
            :data-tooltip="dis[ind] ? '已呈交此时间段的活动': '不可选择此时间段'">
              <i class="icon icon-arrow-right mr-1"></i>
              {{ section }}
              <i class="feather icon-lock ml-2"
              v-if="dis[ind] || invalidSelect(ind)"></i>
            </div>
            <transition name="fade">
              <small class="label label-rounded label-primary"
              v-if="dis[ind]">已呈交：{{ name[ind] }}</small>
              <small class="label label-rounded label-primary"
              v-else-if="id[ind]">已选：{{ name[ind] }}</small>
            </transition>
          </label>
          <div class="accordion-body">
            <div class="empty-lesson text-gray ml-2 mt-2 text-italic"
            v-if="lessons[ind].length == 0">
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
        @click="submit">
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

import card from '@/components/card';
import headerLayout from '@/layout/header';

export default {
  components: {
    card,
    headerLayout,
  },
  mounted() {
    this.init();
  },
  data: () => ({
    isPageLoading: true,
    isSubmitLoading: false,
    titles: ['第 1 - 3 节', '第 4 - 5 节', '第 6 - 7 节', '第 4 - 7 节'],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7]],
    selectedBool: new Array(4).fill(false), // to prevent collission selections
    actives: new Array(7).fill(false), // current chosen periods before submission
    chosen: new Array(7).fill(false), // chosen periods from user object
    chosenId: [], //chosen period IDs from user object
    dis: new Array(4).fill(false), // disable accordions for submitted sessions
    locked: new Array(4).fill(false), // forced lessons
    user: {},
    lessons: [[], [], [], []], // display data
    year: 0,
    logoutLoad: false,
  }),
  methods: {
    ...mapMutations('lessons', {
      select: 'SELECT_LESSON',
      reset: 'RESET'
    }),
    init() {
      getUser().then(({data}) => {
        this.user = data;
        this.isPageLoading = false;
        // console.log(this.user)
        // get current year of user
        this.year = (new Date().getFullYear() % 100) - parseInt(this.user.id.toString().substr(0, 2)) + 1;
        getAllLessons().then(({data}) => {
          // find lessons valid for user's year and sort lessons according to period sessions
          let lessonArr = data.filter(el => el.year.indexOf(this.year) != -1)
          lessonArr.forEach(el => {
            for (let i = 0; i < 4; i++)
              if (JSON.stringify(el.period.sort()) == JSON.stringify(this.sessions[i])) 
                this.lessons[i].push(el)
          })
          // set user submit lessons as active
          this.user.lessons.forEach(el => {
            this.chosen[Object.keys(el)[0]-1] = true;
            for (let i = 0; i < 4; i++) {
              if (this.sessions[i].indexOf(parseInt(Object.keys(el)[0])) != -1) {
                this.selectedBool[i] = true; 
                this.dis[i] = true;
                this.sessions[i].forEach(elem => this.actives[elem-1] = true);
                this.chosenId.push(el[Object.keys(el)[0]]);
                this.select({ind: i, id: el[Object.keys(el)[0]], name: this.lessons[i].filter(elem => elem.id == el[Object.keys(el)[0]])[0].name})
                break;
              }
            }
          })
          // set initially chosen (haven't submit) lessons and progress bar
          this.id.forEach((el, ind) => {
            if (el) {
              this.selectedBool[ind] = true;
              this.sessions[ind].forEach(elem => this.actives[elem-1] = true);
            }
          })
        })
      })
    },
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
        // Deactivate card and progress bar
        this.selectedBool[ind] = false;
        this.sessions[ind].forEach(el => this.actives[el-1] = false);
        this.select({ind, id: 0, name: ""});
      } else {
        // Activate card and progress bar
        this.selectedBool[ind] = true;
        this.sessions[ind].forEach(el => this.actives[el-1] = true);
        this.select({ind, id, name})
      }
      // Update accordion and cards
      this.$forceUpdate();
    },
    submit() {
      this.isSubmitLoading = true;
      submitUser({ lessons: this.id.filter(el => el != 0 && this.chosenId.indexOf(el) == -1) })
        .then((data) => {
          this.init();
        }).finally(() => this.isSubmitLoading = false)
    },
    invalidSelect(ind) {
      return (ind == 1 || ind == 2) ? this.selectedBool[3] : ind == 3 ? (this.selectedBool[1] || this.selectedBool[2]) : false
    },
  },
  computed: {
    ...mapState('lessons', {
      id: 'selected_lesson_id',
      name: 'selected_lesson_name'
    }),
  },
}
</script>

<style lang="scss">

</style>