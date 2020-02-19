<template>
  <div id="_edit_lesson">
    <div class="form-group">
      <select class="form-select mb-2 col-6" v-model="session">
        <option :value="-1" disabled selected>请选择阶段</option>
        <option v-for="(session, id) in titles" :key="id"
        :value="id">{{session}}</option>
      </select>
    </div>
    <lesson-table ref=table title
    navbar="活动名称"
    :columns="lesson_list_columns"
    :tableData="selected_lessons">
      <template slot="title">
        {{ titles[session] }}
      </template>
      <template slot="action" slot-scope="{ data }">
        <button class="btn btn-secondary mr-2" @click="edit(data)">更改</button>
        <button class="btn btn-error ml-2">删除</button>
      </template>
    </lesson-table>
  </div>
</template>

<script>
import { mapMutations, mapState } from 'vuex';
import { lesson_list_columns } from '@/api/tableColumns';

import lessonTable from '@/components/table';

export default {
  components: {
    lessonTable,
  },
  data: () => ({
    lesson_list_columns,
    selected_lessons: [],
    session: -1,
    titles: ['第 1 - 3 节', '第 4 - 5 节', '第 6 - 7 节', '第 4 - 7 节', '第 1 - 7 节'],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7], [1,2,3,4,5,6,7]],
  }),
  methods: {
    ...mapMutations('admin_edit', {
      set: 'SET_LESSON'
    }),
    edit(lesson) {
      this.set(lesson);
      this.$router.push('/admin/edit_lesson/details');
    }
  },
  computed: {
    ...mapState('admin_lessons', {
      lessons: 'lessons'
    })
  },
  watch: {
    session(val) {
      this.selected_lessons = this.lessons.filter(el => JSON.stringify(el.period) == JSON.stringify(this.sessions[val]));
    }
  }
}
</script>

<style lang="scss">

</style>