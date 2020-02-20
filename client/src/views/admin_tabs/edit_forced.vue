<template>
  <div id="_edit_forced">
    <div class="columns selection">
      <div class="form-group column col-6 col-xs-12">
        <div class="form-label">时段</div>
        <select class="form-select" name="session" id="session" v-model="selected_session">
          <option :value="-1" selected disabled>请选择时段</option>
          <option 
          v-for="(session, id) in session_names" 
          :key="id"
          :value="id">{{ session }}</option>
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

    <div class="columns data-section">
      <cp-table class="column col-6 col-md-12" ref="table" title hoverable
      emptyMessage="还未输入学生"
      :columns="forced_user_columns"
      :tableData="table_data">
        <template slot="title" v-if="table_data.length">
          保留{{ session_names[selected_session] }}（{{selected_name}}）
        </template>
      </cp-table>
      <!-- <pre>{{table_data}}</pre> -->
      <div class="column col-6 col-md-12">
        <textarea class="form-input" rows="5"
        v-model="student_id"></textarea>
        <div class="btn-group btn-group-block mt-2">
          <button class="btn btn-primary"
          @click="dataImport">
            输入学生 <i class="feather icon-clipboard"></i>
          </button>
          <button class="btn btn-secondary"
          @click="table_data = []">
            清空列表 <i class="feather icon-trash-"></i>
          </button>
          <button class="btn btn-success" :class="{'loading': addLoad}"
          @click="add" :disabled="!afterImport">
            加入保留 <i class="feather icon-edit"></i>
          </button>
          <button class="btn btn-error" :class="{'loading': removeLoad}"
          @click="remove" :disabled="!afterImport">
            删除保留 <i class="feather icon-delete"></i>
          </button>
        </div>
      </div>
    </div>


  </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';
import { addForcedLesson, removeForcedLesson } from '@/api/lesson';
import { forced_user_columns } from '@/api/tableColumns';

import cpTable from '@/components/table';

export default {
  components: {
    cpTable,
  },
  mounted() {
    this.$refs.table.is_loading = false;
  },
  data: () => ({
    forced_user_columns,
    table_data: [],
    selected_name: "",
    selected_session: -1,
    selected_lessons: [],
    selected_lessons_name: [],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7], [1,2,3,4,5,6,7]],
    session_names: ['第 1 - 3 节', '第 4 - 5 节', '第 6 - 7 节', '第 4 - 7 节', '第 1 - 7 节 '],
    student_id: "",

    addLoad: false,
    removeLoad: false,
    afterImport: false,
  }),
  methods: {
    dataImport() {
      let ids = this.student_id.split("\n");
      ids.forEach(el => {
        let found = this.users.filter(elem => elem.id == el);
        if (found.length) {
          // let before = found[0].lessons.map()
          this.table_data.push(found[0]);
        }
        else el.concat(" => 该学号的学生不存在");
      });
      this.student_id = ids.join("\n");
      this.afterImport = true;
    },
    add() {
      if (!this.selected_name) {
        this.$notify({
          type: 'warn',
          title: '请选择保留的活动'
        })
        return ;
      }
      this.addLoad = true;
      addForcedLesson(
        this.selected_lessons
          .filter(el => el.name == this.selected_name)[0].id,
        { students: this.table_data.map(el => el.id) }
      ).then((data) => {
        if (data.status == 200) {
          this.table_data = [];
          this.student_id = "";
          this.$notify({
            type: 'success',
            title: '成功加入保留活动中'
          });
        }
      }).finally(() => this.addLoad = false)
    },
    remove() {
      if (!this.selected_name) {
        this.$notify({
          type: 'warn',
          title: '请选择保留的活动'
        })
        return ;
      }
      if (!this.table_data) {
        this.$notify({
          type: 'warn',
          title: '请选择输入学生'
        })
        return ;
      }
      this.removeLoad = true;
      removeForcedLesson(
        this.selected_lessons
          .filter(el => el.name == this.selected_name)[0].id,
        { students: this.table_data.map(el => el.id) }
      ).then((data) => {
        if (data.status == 200) {
          this.table_data = [];
          this.student_id = "";
        }
      }).finally(() => this.removeLoad = false)
    }
  },
  computed: {
    ...mapState('admin_data', {
      lessons: 'lessons',
      users: 'users'
    })
  },
  watch: {
    selected_session(val) {
      this.selected_name = "";
      if (val < 0) this.selected_lessons = this.lessons;
      else {
        let ss = JSON.stringify(this.sessions[val]);
        this.selected_lessons = this.lessons.filter(el => JSON.stringify(el.period) == ss);
      }
      this.selected_lessons_name = this.selected_lessons.map(el => el.name);
    },
    student_id(val) {
      this.afterImport = false;
      let idArr = val.split("\n");
      if (/[^0-9]/.test(idArr[idArr.length - 1])) 
        this.$notify({
          type: 'warn',
          title: '只可输入学号（数字）'
        })
    }
  }
}
</script>

<style lang="scss">

</style>