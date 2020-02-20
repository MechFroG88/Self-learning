<template>
  <div id="_search" :class="{'loading loading-lg': isPageLoading}">
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
          <select class="form-select" name="lessons_name" id="lessons_name" v-model="selected_name">
            <option :value="''" selected>--</option>
            <option 
            v-for="(lesson, id) in selected_lessons_name"
            :key="id"
            :value="lesson">{{ lesson }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="all-students" v-if="data_type == 2">
      <div class="columns">
        <div class="form-group column col-6 col-xs-12">
          <div class="form-label">年级</div>
          <select class="form-select" name="year" id="year" v-model="selected_year">
            <option :value="''" selected>--</option>
            <option 
            v-for="(year, id) in years" 
            :key="id"
            :value="year">{{ year }}</option>
          </select>
        </div>
        <div class="form-group column col-6 col-xs-12">
          <div class="form-label">班级</div>
          <select class="form-select" name="class" id="class" v-model="selected_class">
            <option :value="''" selected>--</option>
            <option 
            v-for="(classname, id) in classnames" 
            :key="id"
            :value="classname">{{ classname }}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="button-group mb-2">
      <vue-excel-xlsx
        v-if="showTable
        && (data_type == 1 ? !!student_table_list.length : true)"
        class="btn btn-primary mr-2"
        :data="student_table_list"
        :columns="
          data_type == 1 ? selected_name ? 
          lesson_columns : lesson_name_columns :
          selected_class.length ? student_columns : student_class_columns"
        :filename="
          data_type == 1 ? selected_name ?
          `${filenames[selected_session]}(${selected_name})` : filenames[selected_session] :
          selected_year+selected_class"
        :sheetname="'sheet1'"
        >
        输出&下载excel文档
      </vue-excel-xlsx>
      <div class="btn btn-primary ml-2" :class="{'loading loading-lg': isCheckLoading}"
      @click="check(data_type)">
        查询 <i class="feather icon-check"></i>
      </div>
    </div>

    <data-table ref="table" class="mt-2"
    v-if="showTable" title hoverable
    emptyMessage="此活动暂时无学生选择"
    navbar="学生学号"
    :columns="
      data_type == 1 ? selected_name ? 
      lesson_columns : lesson_name_columns :
      selected_class.length ? student_columns : student_class_columns"
    :tableData="student_table_list">
      <template slot="title" v-if="data_type == 2">
        {{ selected_year+selected_class}}
      </template>
    </data-table>
  </div>
</template>

<script>
import { mapState } from 'vuex';
import { userLogout } from '@/api/user';
import { getAllClasses } from '@/api/class';
import { getLessonUsers, getLessonsList } from '@/api/lesson';
import { lesson_columns, student_columns, student_class_columns, lesson_name_columns } from '@/api/tableColumns';

import dataTable from '@/components/table';

export default {
  components: {
    dataTable
  },
  data: () => ({
    isPageLoading: true,
    isCheckLoading: false,
    data_type: 1,

    lesson_columns,
    lesson_name_columns,
    logout_load: false,
    selected_name: "",
    selected_session: -1,
    selected_lessons: [],
    selected_lessons_name: [],
    selected_lessons_id: [],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7], [1,2,3,4,5,6,7]],
    filenames: ['第1-3节', '第4-5节', '第6-7节', '第4-7节', '第1-7节 '],

    classes: [],
    classnames: [],
    student_columns,
    student_class_columns,
    years: ['初一', '初二', '初三', '高一理', '高一文', '高二理', '高二文', '高三理', '高三文', '高三商'],
    selected_year: "",
    selected_class: "",

    showTable: false,
    student_table_list: [],
  }),
  mounted() {
    getAllClasses() .then(({data}) => { 
      this.classes = data; 
      this.classnames = this.classes
                          .filter(el => el.cn_name.includes('初一'))
                          .map(el => el.cn_name[el.cn_name.length-2]);
      this.isPageLoading = false;
    });
  },
  methods: {
    check(type) {
      this.isCheckLoading = true;
      this.showTable = true;
      this.$nextTick(() => {
        // Search by session and name
        if (type == 1) {
          this.student_table_list = [];
          this.selected_lessons
            // Find all selected_lessons of selected_session by name (if specified)
            .filter(el => el.name == this.selected_name || !this.selected_name)
            // Concat all the students associated with said lessons into table data
            .forEach(el => this.student_table_list = this.student_table_list.concat(
              this.students
                .filter(elem => elem.lessons.map(element => Object.values(element)[0]).indexOf(el.id) != -1 
                                        || elem.forced_lessons.map(element => Object.values(element)[0]).indexOf(el.id) != -1)
                // Apply property of lesson_name in case querying for whole session
                .map(elem => ({...elem, lesson_name: el.name}))
                // Sort by year then by classname
                .sort((a, b) => {
                    let yearcmp = this.years.indexOf(a.class.substr(0,a.class.length-3))-this.years.indexOf(b.class.substr(0,b.class.length-3)),
                    classnamescmp = this.classes.filter(el => el.cn_name.includes(a.class.substr(0,a.class.length-3))).map(el => el.cn_name[el.cn_name.length-2]);
                    return yearcmp == 0 ? 
                      classnamescmp.indexOf(a.class[a.class.length-2])-classnamescmp.indexOf(b.class[b.class.length-2])
                    : yearcmp;
                })
            ))
        }
        
        // Search by year or class
        else {
          // Find all students of said year and/or class (if specified)
          this.student_table_list = this.students.filter(
                                      el => el.class.includes(this.selected_year) 
                                          && el.class.includes(this.selected_class));
          
          // Get names of lessons selected by every student and categorize into sessions
          this.student_table_list.forEach(el => {
            let names = ["", "", ""];
            [1,4,6].forEach((elem, ind) => {
              let id_obj = el.lessons.filter(element => Object.keys(element).indexOf(elem.toString()) != -1)[0] 
                        || el.forced_lessons.filter(element => Object.keys(element).indexOf(elem.toString()) != -1)[0];
              names[ind] = id_obj ? 
                `${this.lessons.filter(element => element.id == id_obj[elem])[0].name}（${this.lessons.filter(element => element.id == id_obj[elem])[0].location}）`
                : "--";
            })
            el.first = names[0];
            el.second = names[1];
            el.third = names[2];
          });

          // Sort by class (and year) if querying for whole year/school
          this.student_table_list = this.student_table_list.sort((a, b) => {
            let classcmp = this.classnames.indexOf(a.class[a.class.length-2])-this.classnames.indexOf(b.class[b.class.length-2]),
                yearcmp = this.years.indexOf(a.class.substr(0,a.class.length-3))-this.years.indexOf(b.class.substr(0,b.class.length-3)),
                gendercmp = a.gender == b.gender ? 0 : a.gender == '女' ? -1 : 1;
            return yearcmp == 0 ? 
              classcmp == 0 ?
                gendercmp == 0 ?
                  a.class_no-b.class_no
                : gendercmp 
              : classcmp
            : yearcmp;
          });
        }
        this.isCheckLoading = false;
      })
    },
    logout() {
      this.logout_load = true;
      userLogout().then((data) => {
        if (data.status == 200) {
          this.$router.push('/');
        }
      }).finally(() => this.logout_load = false)
    }
  },
  computed: {
    ...mapState('admin_data', {
      lessons: 'lessons',
      students: 'users',
    })
  },
  watch: {
    data_type: function() { this.showTable = false; },
    selected_name: function() { this.showTable = false; },
    selected_class: function() { this.showTable = false; },
    selected_session(val) {
      if (this.data_type != 1) return ;
      this.showTable = false;
      if (val < 0) this.selected_lessons = this.lessons;
      else {
        let ss = JSON.stringify(this.sessions[val]);
        this.selected_lessons = this.lessons.filter(el => JSON.stringify(el.period) == ss);
      }
      this.selected_lessons_name = this.selected_lessons.map(el => el.name);
      this.selected_lessons_id = this.selected_lessons.map(el => el.id);
    },
    selected_year(val) {
      this.showTable = false;
      if (val == '' || !val) this.classnames = [];
      else this.classnames = this.classes
                              .filter(el => el.cn_name.includes(val))
                              .map(el => el.cn_name[el.cn_name.length-2]);
    },
  }
}
</script>

<style lang="scss">

</style>