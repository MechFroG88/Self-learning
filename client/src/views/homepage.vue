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
        <div class="accordion-container" v-if="!isPageLoading">
          <div class="accordion" :class="{'disabled': dis[ind]}"
          v-for="(section, ind) in titles" :key="section">
            <input type="radio" :id="section" name="accordion-checkbox" hidden :disabled="dis[ind] || invalidSelect(ind)">
            <label class="accordion-header" :for="section">
              <div class="accordion-header-section" :class="{'tooltip': dis[ind] || invalidSelect(ind)}"
              :data-tooltip="dis[ind] ? '已呈交此时间段的活动': '此时间段内已选择其他活动'">
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
              <div class="lesson-row">
                <div class="lesson-col" v-for="c in Math.ceil(lessons[ind].length/rowSize)" :key="c">
                  <card
                  v-for="lesson in lessons[ind].slice((c-1)*rowSize, c*rowSize)" :key="lesson.id"
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
            </div>
          </div>
        </div>
        
        <div class="btn btn-lg btn-secondary submit" :class="{'loading': isSubmitLoading}"
        @click="$refs.confirm.active = true">
          提交 <i class="feather icon-arrow-right"></i>
        </div>
      </div>
    </div>

    <modal title="确认选择以下活动" ref="confirm"
    :bodyData="[lessonArr, id, chosenId]">
      <template v-slot:body="{ data }">
        <ul v-if="data[1].filter(el => el != 0 && data[2].indexOf(el) == -1).length">
          <li v-for="single_id in data[1].filter(el => el != 0 && data[2].indexOf(el) == -1)" :key="single_id">
            <template v-if="data[0].filter(el => el.id == single_id).length">
              {{ data[0].filter(el => el.id == single_id)[0].name }}
            </template>
          </li>
          <div class="text-small text-gray" style="margin-top: 1rem;">提交后将无法更改，是否确认提交？</div>
        </ul>
        <div v-else>还未选择任何活动</div>
      </template>
      <template v-slot:footer>
        <div class="btn btn-primary" :class="{'loading': isSubmitLoading}" 
        @click="submit">确认</div>
      </template>
    </modal>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
import { userLogout, getUser, getUserLessons, submitUser } from '@/api/user';

import headerLayout from '@/layout/header';
import card from '@/components/card';
import modal from '@/components/modal';

export default {
  components: {
    headerLayout,
    card,
    modal,
  },
  mounted() {
    this.init();
    this.rowSize = this.defaultRowSize;
    if (window.innerWidth > 840) this.rowSize = 1;
    window.addEventListener('resize', () => {
      if (window.innerWidth > 840) this.rowSize = 1;
      else this.rowSize = this.defaultRowSize;
    })
  },
  data: () => ({
    defaultRowSize: 2, // row size for phone/small window mode
    rowSize: null,
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
    lessonArr: [], // all lessons
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
        // get current year of user
        this.year = (new Date().getFullYear() % 100) - parseInt(this.user.id.toString().substr(0, 2)) + 1;
        getUserLessons().then(({data}) => {
          // categorise lessons according to sessions
          this.lessonArr = data;
          this.lessonArr.forEach(el => {
            for (let i = 0; i < this.lessons.length; i++)
              if (JSON.stringify(el.period.sort()) == JSON.stringify(this.sessions[i])) 
                this.lessons[i].push(el)
          })
          // set user submit lessons as active
          this.user.lessons.forEach(el => {
            this.chosen[Object.keys(el)[0]-1] = true;
            for (let i = 0; i < this.sessions.length; i++) {
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
      // Ignore click when user has already selected activity with same name in other sessions
      if (this.name.indexOf(name) != -1) {
        this.$notify({
          type: 'warn',
          title: '无法选择',
          text: '此活动已在其他时间段选择'
        });
        return ;
      }

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
      // Filter selected IDs for sessions user hasn't selected for
      let arr = this.id.filter(el => el != 0 && this.chosenId.indexOf(el) == -1);
      if (arr.length == 0) return ;
      submitUser({ lessons: arr })
        .then((data) => {
          this.$refs.confirm.active = false;
          this.init();
          this.$forceUpdate();
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