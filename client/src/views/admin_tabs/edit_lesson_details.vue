<template>
  <div id="_details">
    <form class="form-horizontal">
      <div class="form-group">
        <div class="col-12">
          <label class="form-label" for="name">活动名称</label>
        </div>
        <div class="col-6 col-sm-12">
          <input class="form-input" type="text" id="name" placeholder="活动名称"
          v-model="lesson_data.name">
        </div>
      </div>

      <div class="form-group">
        <div class="col-12">
          <label class="form-label" for="subject">负责单位</label>
        </div>
        <div class="col-6 col-sm-12">
          <input class="form-input" type="text" id="subject" placeholder="负责单位"
          v-model="lesson_data.subject">
        </div>
      </div>
      
      <div class="form-group">
        <div class="col-12">
          <label class="form-label" for="location">活动地点</label>
        </div>
        <div class="col-6 col-sm-12">
          <input class="form-input" type="text" id="location" placeholder="活动地点"
          v-model="lesson_data.location">
        </div>
      </div>

      <div class="form-group-row columns">
        <div class="form-group column col-4">
          <label class="form-label" for="limit">限制人数</label>
          <input class="form-input" type="text" id="limit" placeholder="限制人数"
          v-model="lesson_data.limit">
        </div>
        <div class="form-group column col-2">
          <label class="form-label" for="gender">性别</label>
          <select class="form-select" id="gender" v-model="lesson_data.gender">
            <option value="无">无</option>
            <option value="男">男</option>
            <option value="女">女</option>
          </select>
        </div>
        <div class="form-group column col-2">
          <label class="form-label" for="stream">文理科</label>
          <select class="form-select" id="stream" v-model="lesson_data.stream">
            <option value="无">无</option>
            <option value="文">文</option>
            <option value="理">理</option>
          </select>
        </div>
        <div class="form-group column col-4">
          <label class="form-label" for="period">阶段</label>
          <select class="form-select" id="period" v-model="lesson_data.period">
            <option v-for="(title, id) in titles" :key="id" 
            :value="JSON.parse(JSON.stringify(sessions[id]))">
              {{ title }}
            </option>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label col-12" style="margin-right: 1rem">年级</label>
        <div class="col-6 col-md-12">
          <label class="form-checkbox form-inline" v-for="(year, id) in years" :key="id">
            <input type="checkbox" v-model="year_check[id]">
            <i class="form-icon"></i> {{year}}
          </label>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label col-12" for="descripttion">活动详情/需求</label>
        <textarea class="form-input col-8 col-sm-12" id="descripttion" rows="5"
        v-model="lesson_data.description"></textarea>
      </div>
    </form>

    <div class="form-group" v-if="$route.params.type == 'add'">
      <label class="form-switch">
        <input type="checkbox" v-model="isForced">
        <i class="form-icon"></i> 将此活动设定为保留活动
      </label>
    </div>

    <div class="btn btn-primary mt-2" :class="{'loading': btnLoading}"
    @click="submit">
      提交 <i class="feather icon-check"></i>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations  } from 'vuex';
import { createLesson, editLesson, getAllLessons } from '@/api/lesson';

export default {
  data: () => ({
    lesson_data: {
      name: '',
      location: '',
      subject: '',
      limit: 0,
      current: 0,
      stream: '无',
      gender: '无',
      description: '',
      period: [],
      year: [],
      btnLoading: false,
    },
    isForced: false,
    titles: ['第 1 - 3 节', '第 4 - 5 节', '第 6 - 7 节', '第 4 - 7 节', '第 1 - 7 节'],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7], [1,2,3,4,5,6,7]],
    years: ['初一', '初二', '初三', '高一', '高二', '高三'],
    year_check: new Array(6).fill(false),
  }),
  mounted() {
    if (this.$route.params.type == 'edit') {
      this.lesson_data = Object.assign({}, this.lesson);
      delete this.lesson_data.id;
    }
    this.lesson_data.year.forEach(el => this.year_check[el-1] = true);
  },
  methods: {
    ...mapMutations('admin_edit', {
      reset: 'RESET_LESSON'
    }),
    ...mapMutations('admin_data', {
      setLessons: 'SET_LESSONS'
    }),
    submit() {
      this.btnLoading = true;
      if (this.$route.params.type == 'edit') {
        editLesson(this.lesson.id, this.lesson_data)
          .then((data) => {
            if (data.status == 200) {
              this.reset();
              getAllLessons().then(({data}) => { 
                this.setLessons(data); 
                this.$router.push('/admin/edit_lesson');
              }).finally(() => this.btnLoading = false);
            }
          }).finally(() => this.btnLoading = false);
      }
      else {
        if (this.isForced) this.lesson_data.current = this.lesson_data.limit;
        createLesson(this.lesson_data)
          .then((data) => {
            if (data.status == 200) {
              this.reset();
              getAllLessons().then(({data}) => { 
                this.setLessons(data); 
                this.$router.push('/admin/edit_lesson');
              }).finally(() => this.btnLoading = false);
            }
          }).finally(() => this.btnLoading = false);
      }
    }
  },
  computed: {
    ...mapState('admin_edit', {
      lesson: 'lesson'
    }),
  },
  watch: {
    year_check: {
      handler: function(val) {
        val.forEach((el, ind) => {
          let period_ind = this.lesson_data.year.indexOf(ind+1);
          if (el) {
            if (period_ind == -1) this.lesson_data.year.push(ind+1);
          }
          else if (period_ind != -1) {
            this.lesson_data.year.splice(period_ind, 1);
          }
        })
        this.lesson_data.year.sort();
      },
      deep: true,
    }
  }
}
</script>

<style lang="scss">

</style>