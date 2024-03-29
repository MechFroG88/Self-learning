<template>
  <div id="_homepage" :class="{'loading loading-lg': isPageLoading}">
    <div v-if="masterPeriod || new Date().getTime() <= new Date(periodEnd).getTime()">
      <header-layout 
      :cn_name="user.cn_name"
      :id="user.id"></header-layout>

      <div class="body columns">
        <div class="sidebar col-2 col-xs-3">
          <div class="period-counter" v-if="!isPageLoading">
            <h4 style="text-align: center; margin-bottom: 1rem;">节数</h4>
            <div class="cell" v-for="i in 7" :key="i" 
            :class="{
              'active': actives[i-1],
              'chosen': chosen[i-1],
            }">
              <div class="period">{{i}}</div>
              <div class="progress-bar"></div>
            </div>
          </div>
          <div class="popover popover-right">
            <div class="settings">
              <i class="feather icon-settings mr-1"></i>设置
            </div>
            <div class="popover-container">
              <div class="card">
                <div class="card-body">
                  <span class="delete mb-2" @click="openDelete">
                    <i class="feather icon-trash- mr-2"></i>删除
                  </span>
                  <span class="logout mt-2" @click="logout"
                  :class="{'loading': logoutLoad}">
                    <i class="feather icon-log-out mr-2"></i>登出
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="content column col-10 col-xs-9">
          <div class="accordion-container" v-if="!isPageLoading">
            <div class="accordion" :class="{'disabled': dis[ind]}"
            v-for="(section, ind) in titles" :key="section">
              <!-- Accordion header -->
              <input type="checkbox" :id="section" name="accordion-checkbox" hidden v-model="accordions[ind]">
              <label class="accordion-header" :for="section">
                <div class="accordion-header-section">
                  <i class="icon icon-arrow-right mr-1"></i>
                  {{ section }}
                </div>
                <transition name="fade">
                  <small class="label label-rounded label-success"
                  v-if="locked[ind]">已保留：{{ name[ind] }}</small>
                  <small class="label label-rounded label-success"
                  v-else-if="dis[ind]">已呈交：{{ name[ind] }}</small>
                  <small class="label label-rounded label-primary"
                  v-else-if="id[ind]">已选：{{ name[ind] }}</small>
                </transition>
              </label>
              <!-- Accordion header -->

              <!-- Accordion body -->
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
                    :initials="lesson.subject[0]"
                    :pax="lesson.limit"
                    :num="lesson.current"
                    :classroom="lesson.location"
                    :disable="dis[ind] || invalidSelect(ind) || locked[ind] || lesson.current >= lesson.limit"
                    :disableMsg="locked[ind] ? '此阶段已有保留的活动'
                        : invalidSelect(ind) ? '无法选择此时间段内的活动' 
                                  : dis[ind] ? '此时间段内已呈交活动' 
                                            : '此活动人数已满，请选择其他活动'"
                    :bg-color="color[lesson.subject]"
                    :active="id[ind] == lesson.id"
                    class="c-hand"
                    @clicked="choose(ind, lesson.id, lesson.name)"
                    @action="details(ind, lesson)"></card>
                  </div>
                </div>
              </div>
              <!-- Accordion body -->
            </div>
          </div>

          <button class="btn btn-lg btn-secondary submit" :class="{'loading': isSubmitLoading, 'tooltip tooltip-left': tempDisableSubmit}"
          :data-tooltip="`还未开放提交！ \n 提交开放时段： \n 18/2 20:00 - 21/2 20:00`"
          v-if="!disableSubmit" @click="tempDisableSubmit ? () => {} : $refs.confirm.active = true">
            提交 <i class="feather icon-arrow-right"></i>
          </button>
          <button class="btn btn-lg btn-secondary submit"
          v-else @click="$refs.list.active = true">
            查阅 <i class="feather icon-check"></i>
          </button>
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
            <div class="text-small text-gray text-italic text-bold" style="margin-top: 1rem; margin-left: -.8rem;">
              <span>
                点击选择栏的 
                </span><i class="icon icon-more-vert text-primary"></i><span>
                可查阅活动详情与须知。
              </span>
            </div>
          </ul>
          <div v-else>还未选择任何活动</div>
        </template>
        <template v-slot:footer>
          <div class="btn btn-primary" :class="{'loading': isSubmitLoading}" 
          @click="submit">确认</div>
        </template>
      </modal>

      <modal title="已呈交选择以下活动" ref="list" v-if="disableSubmit"
      :bodyData="[name]">
        <template v-slot:body="{ data }">
          <ul>
            <li v-for="name in data[0].filter(el => el.replace(/\s/g, '').length != 0)" :key="name">
              <template>
                {{ name }}
              </template>
            </li>
          </ul>
        </template>
      </modal>

      <modal :title="detailLesson.name" ref="detail" :bodyData="detailLesson">
        <template v-slot:body="{ data }">
          <h6 v-if="data.description"><b>
            {{ data.description }}
          </b></h6>
          <h6>
            人数：{{ `${data.current}/${data.limit}` }}
          </h6>
          <h6>{{ data.location }}</h6>
          <h6>
            {{ data.subject }}
            <em>（{{ titles[data.ind] }}）</em>
          </h6>
        </template>
      </modal>

      <modal title="删除已呈交的活动" ref="delete"
      :bodyData="[toDelete]">
        <template v-slot:body="{ data }">
          <div class="form-group"
          v-for="(name, ind) in data[0].name" :key="ind">
            <label class="form-checkbox">
              <input type="checkbox" class="form-input" 
              v-model="data[0].data[ind]">
              <i class="form-icon"></i> {{ name }}
            </label>
          </div>
        </template>
        <template slot="footer" v-if="toDelete.name.length">
          <div class="btn btn-error float-right" :class="{'loading': deleteLoad}"
          @click="deleteLessons">
            删除
          </div>
        </template>
      </modal>
    </div>

    <div class="thanks" v-else>
      <div class="text-primary text-bold h1 title">呈交已结束</div>
      <div class="h4 content">
        <p>
          非常感谢您使用此网站进行开放学习日的活动选择。
          倘若在选择或呈交活动期间对您造成任何不便，还请多多见谅！
        </p>
        <div>您已选择：</div>
        <ul>
          <li v-for="(n, ind) in name.filter(el => el.replace(/\s/g, '').length != 0)" 
            :key="ind">{{n}}</li>
        </ul>
      </div>
      <div class="btn btn-primary logout" @click="logout">
        <i class="feather icon-log-out"></i> 登出
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment';
import { mapState, mapMutations } from 'vuex';
import { userLogout, getUser, removeUserLessons, submitUser } from '@/api/user';
import { getAllLessons } from '@/api/lesson';
import colors from '@/api/colors.json';

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
    let now = new Date().getTime(), 
      start = new Date(this.periodStart).getTime(), 
        end = new Date(this.periodEnd).getTime();

    this.tempDisableSubmit &= now < start || now > end;
    if (this.masterPeriod) this.tempDisableSubmit = false;

    if (start-now > 0) {
      setTimeout(() => {
        this.tempDisableSubmit = false;
      }, start-now+500);
    }

    // Change the view mode for different screen sizes
    this.rowSize = this.defaultRowSize;
    if (window.innerWidth > 840) this.rowSize = 1;
    window.addEventListener('resize', () => {
      if (window.innerWidth > 840) this.rowSize = 1;
      else this.rowSize = this.defaultRowSize;
    })
  },
  data: () => ({
    masterPeriod: false, // force the period to be ignored and always allow submission page to show
    periodStart: '2020-02-18T20:00:00', // YYYY-MM-DDTHh:Mm:Ss
    periodEnd: '2020-02-21T20:00:00',
    tempDisableSubmit: process.env.NODE_ENV == 'production', // temporarily disable button for non-submitting periods
    defaultRowSize: 2, // row size for phone/small window mode
    rowSize: null,
    isPageLoading: true,
    isSubmitLoading: false,
    titles: ['第 1 - 3 节', '第 4 - 5 节', '第 6 - 7 节', '第 4 - 7 节', '第 1 - 7 节'],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7], [1,2,3,4,5,6,7]],
    accordions: null,
    selectedBool: new Array(5).fill(false), // to prevent collission selections
    dis: new Array(5).fill(false), // disable accordions for submitted sessions
    locked: new Array(5).fill(false), // forced lessons
    lessons: null, // display data
    actives: new Array(7).fill(false), // current chosen periods before submission
    chosen: new Array(7).fill(false), // chosen periods from user object
    chosenId: [], // chosen period IDs from user object
    color: {},
    colors,
    disableSubmit: false, // disable submission if finsihed
    lessonArr: [], // all lessons
    detailLesson: {}, // lesson to have details displayed
    toDelete: {
      name: [],
      id: [],
      data: [],
    }, // for delete lessons
    year: 0,
    logoutLoad: false,
    deleteLoad: false,
  }),
  methods: {
    ...mapMutations('lessons', {
      select: 'SELECT_LESSON',
      reset: 'RESET'
    }),
    ...mapMutations('user', {
      loginUser: 'LOGIN',
      logoutUser: 'LOGOUT'
    }),
    init() {
      this.accordions = new Array(5).fill(false);
      this.lessons = [[],[],[],[],[]];
      this.chosenId = [];
      this.selectedBool = new Array(5).fill(false);
      this.dis = new Array(5).fill(false);
      this.locked = new Array(5).fill(false);
      this.actives = new Array(7).fill(false);
      this.chosen = new Array(7).fill(false);
      
      // get current year of user
      let years = ['初一', '初二', '初三', '高一', '高二', '高三'];
      this.year = years.indexOf(this.user.class.substr(0, 2)) + 1;

      getAllLessons().then(({data}) => {
        let subs = new Set(), count = 0;
        // categorise lessons according to sessions
        this.lessonArr = data
                          .filter(el => ((el.gender == '无' || el.gender == this.user.gender)
                                      && (el.stream == '无' || el.stream == this.user.stream)
                                      && el.year.indexOf(this.year) != -1)
                                      || this.user.forced_lessons.map(elem => Object.values(elem)[0]).indexOf(el.id) != -1)
                          .sort((left, right) => (right.limit-right.current)-(left.limit-left.current));
                          
        this.lessonArr.forEach(el => {
          for (let i = 0; i < this.lessons.length; i++)
            if (JSON.stringify(el.period.sort()) == JSON.stringify(this.sessions[i]) 
                && !this.lessons[i].filter(elem => elem.id == el.id).length)
                  this.lessons[i].push(el);

          // assign a color for each unique subject
          if (!subs.has(el.subject)) {
            this.color[el.subject] = this.colors[count++];
            subs.add(el.subject);
          }
        });

        // set user submit lessons as active
        this.checkId(this.user.lessons);
        // set user force_lessons as active          
        this.checkId(this.user.forced_lessons, true);

        // disable submit button for user who completed submission
        if (this.chosen.indexOf(false) == -1) this.disableSubmit = true;

        // set initially chosen (haven't submit) lessons and progress bar
        this.id.forEach((el, ind) => {
          if (el) {
            this.selectedBool[ind] = true;
            this.sessions[ind].forEach(elem => this.actives[elem-1] = true);
          }
        })

        this.isPageLoading = false;
        if (!this.masterPeriod && new Date().getTime() > new Date(this.periodEnd).getTime()) {
          this.$confetti.start({
            particles: [
              {
                type: 'heart',
                colors: [
                  'red',
                  'pink',
                  'yellow',
                  'blue',
                  '#ba0000',
                ]
              },
              {
                type: 'circle',
                colors: [
                  '#ba0000',
                  'blue',
                  'yellow',
                  'green',
                  'purple'
                ],
              },
              {
                type: 'rect',
                colors: [
                  '#ba0000',
                  'blue',
                  'yellow',
                  'green',
                  'purple'
                ],
              },
            ],
            particlesPerFrame: 0.8,
            defaultDropRate: 6,
            defaultSize: 5,
          });
        }
      })
      .catch((err) => {
        if (err.status) this.logout();
      })
    },
    checkId(lessons, forced=false) {
      let period_lessons = [0,0,0,0,0,0,0];
      lessons.forEach(el => {
        this.chosen[Object.keys(el)[0]-1] = true;
        period_lessons[Object.keys(el)[0]-1] = el[Object.keys(el)[0]];
      });

      for (let i = this.sessions.length-1; i >= 0; i--) {
        let union = true, found = 0;
        for (let j = 0; j < this.sessions[i].length; j++) {
          if ((found != 0 && found != period_lessons[this.sessions[i][j]-1]) || period_lessons[this.sessions[i][j] - 1] == 0) {
            union = false; break;
          }
          found = period_lessons[this.sessions[i][j]-1];
        }
        if (union && found != 0) {
          this.dis[i] = true;
          this.chosenId.push(found);
          this.locked[i] = forced;
          this.selectedBool[i] = true;
          this.sessions[i].forEach(elem => this.actives[elem-1] = true);
          this.select({
            ind: i, 
            id: found, 
            name: this.lessons[i].filter(elem => elem.id == found)[0].name
          })
          this.sessions[i].forEach(el => period_lessons[el-1] = 0);
        }
      }

    },
    choose(ind, id, name) {
      // Ignore click when user has already selected activity with same name in other sessions
      if (this.name.indexOf(name) != -1 && this.id.indexOf(id) == -1) {
        this.$notify({
          type: 'warn',
          title: '无法选择',
          text: '此活动已在其他时间段选择',
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
          if (data.status == 200) {
            this.$refs.confirm.active = false;
            this.$notify({
              type: 'success',
              title: '成功提交！'
            });
            this.isPageLoading = true;
            this.loginUser(data.data);
            this.init();
            this.$forceUpdate();
          }
        })
        .catch((err) => {
          if (err.status == 400) {
            this.$refs.confirm.active = false;
            this.isPageLoading = true;
            this.$notify({
              type: 'error',
              title: '提交失败！',
              text: '您所提交的活动名额已满。请刷新页面后在重试。'
            });
            this.reset();
            this.init();
            this.$forceUpdate();
          }
        }).finally(() => this.isSubmitLoading = false)
    },
    openDelete() {
      this.toDelete = {
        name: [],
        id: [],
        data: []
      };
      
      this.$refs.delete.active = true;

      this.id.forEach((el, ind) => {
        if (this.user.lessons.map(elem => Object.values(elem)[0]).indexOf(el) != -1) {
          this.toDelete.id.push(el);
          this.toDelete.name.push(this.name[ind]);
          this.toDelete.data.push(false);
        }
      })
    },
    deleteLessons() {
      this.deleteLoad = true;
      removeUserLessons({
        lessons: this.toDelete.id.filter((el, ind) => this.toDelete.data[ind])
      }).then((data) => {
          if (data.status == 200) {
            this.deleteLoad = false;
            this.$refs.delete.active = false;
            this.reset();
            this.loginUser(data.data);
            location.reload();
          }
        }).catch((err) => {
          this.deleteLoad = false;
          this.$refs.delete.active = false;
          this.$notify({
            type: 'error',
            title: '无法删除此活动，请稍后再试。'
          });
          this.init();
          this.$forceUpdate();
        })
    },
    details(ind, lesson) {
      this.$refs.detail.active = true;
      this.detailLesson = { ind, ...lesson };
    },
    invalidSelect(ind) {
      // Test if a session is still selectable without conflicts arising
      return (ind == 1 || ind == 2) ? this.selectedBool[3] : ind == 3 ? (this.selectedBool[1] || this.selectedBool[2]) 
            : ind == 4 ? (this.selectedBool[0] || this.selectedBool[1] || this.selectedBool[2] || this.selectedBool[3])
            : false;
    },
    logout() {
      this.logoutLoad = true;
      this.$confetti.stop();
      userLogout().then((data) => {
        if (data.status == 200) {
          this.reset();
          this.logoutUser();
          this.$router.push('/');
        }
      }).finally(() => this.logoutLoad = false)
    },
    time(date) {
      moment.locale("zh-cn");
      return moment().to(date);
    }
  },
  computed: {
    ...mapState('lessons', {
      id: 'selected_lesson_id',
      name: 'selected_lesson_name'
    }),
    ...mapState('user', {
      user: 'user'
    })
  },
}
</script>

<style lang="scss">

</style>