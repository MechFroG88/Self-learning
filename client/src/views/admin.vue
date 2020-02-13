<template>
  <div id="_admin">
    <div class="form-group data-selects">
      <label class="form-radio">
        <input type="radio" name="data_type" :value="1" v-model="data_type">
        <i class="form-icon"></i> 查阅单一活动的学生名单
      </label>
      <label class="form-radio">
        <input type="radio" name="data_type" :value="2" v-model="data_type">
        <i class="form-icon"></i> 查阅所有学生
      </label>
    </div>

    <div class="single-activity" v-if="data_type == 1">
      <div class="columns">
        <div class="form-group column col-6 col-xs-12">
          <div class="form-label">时段</div>
          <select class="form-select" name="session" id="session" v-model="selected_session">
            <option :value="-1" selected disabled>请选择时段</option>
            <option 
            v-for="(session, id) in sessions" 
            :key="id"
            :value="id">第{{session[0]}} - {{session[session.length-1]}}节</option>
          </select>
        </div>
        <div class="form-group column col-6 col-xs-12">
          <div class="form-label">活动名称</div>
          <select class="form-select" name="lessons_name" id="lessons_name" v-model="selected_id">
            <option :value="-1" selected disabled>请选择活动</option>
            <option 
            v-for="(lesson, id) in selected_lessons_name" 
            :key="id"
            :value="selected_lessons_id[id]">{{ lesson }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="all-students" v-if="data_type == 2">
      <div class="columns">
        <div class="form-group column col-6 col-xs-12">
          <div class="form-label">年级</div>
          <select class="form-select" name="year" id="year">
            <option :value="0" selected disabled>请选择年级</option>
          </select>
        </div>
        <div class="form-group column col-6 col-xs-12">
          <div class="form-label">班级</div>
          <select class="form-select" name="class" id="class">
            <option :value="0" selected disabled>请选择班级</option>
          </select>
        </div>
      </div>
    </div>

    <data-table ref="table"
    v-if="showTable" title hoverable
    :navbar="data_type == 1 ? '' : '学生姓名'"
    :columns="data_type == 1 ? lesson_columns : student_columns"
    :tableData="data_type == 1 ? student_list : []">
      <template slot="title">
        {{selected_lesson.name}}
      </template>
    </data-table>

    <div class="btn btn-link mt-2" 
    :class="{'loading': logout_load}"
    @click="logout">登出</div>
  </div>
</template>

<script>
import { userLogout } from '@/api/user';
import { getAllLessons, getLessonUsers } from '@/api/lesson';
import { lesson_columns, student_columns } from '@/api/tableColumns';

import dataTable from '@/components/table';

export default {
  components: {
    dataTable
  },
  data: () => ({
    data_type: 1,
    lessons: [],
    lesson_columns,
    logout_load: false,
    selected_id: -1,
    selected_session: -1,
    selected_lesson: {},
    selected_lessons: [],
    selected_lessons_name: [],
    selected_lessons_id: [],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7]],
    showTable: false,
    student_list: [],
    titles: ['第 1 - 3 节', '第 4 - 5 节', '第 6 - 7 节', '第 4 - 7 节'],
  }),
  mounted() {
    getAllLessons().then(({data}) => {
      this.lessons = data;
    })
  },
  methods: {
    logout() {
      this.logout_load = true;
      userLogout().then((data) => {
        if (data.status == 200) {
          this.$router.push('/');
        }
      }).finally(() => this.logout_load = false)
    }
  },
  watch: {
    data_type() {
      this.showTable = false;
    },
    selected_session(val) {
      this.showTable = false;
      this.selected_id = -1;
      let ss = JSON.stringify(this.sessions[val]);
      this.selected_lessons = this.lessons.filter(el => JSON.stringify(el.period) == ss);
      this.selected_lessons_name = this.selected_lessons.map(el => el.name);
      this.selected_lessons_id = this.selected_lessons.map(el => el.id);
    },
    selected_id(val) {
      this.showTable = false;
      this.selected_lesson = this.selected_lessons.filter(el => el.id == val)[0];
      if (val < 0) return ;
      this.$nextTick(() => {
        this.showTable = true;
        getLessonUsers(val).then(({data}) => {
          this.$refs.table.is_loading = false;
          this.student_list = data;
        })
      })
    }
  }
}
</script>

<style lang="scss">

</style>