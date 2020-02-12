<template>
  <div id="_admin">
    <select name="session" id="session" v-model="selected_session">
      <option :value="-1" selected disabled>请选择时段</option>
      <option 
      v-for="(session, id) in sessions" 
      :key="id"
      :value="id">第{{session[0]}} - {{session[session.length-1]}}节</option>
    </select>
    <select name="lessons_name" id="lessons_name" v-model="selected_id">
      <option :value="-1" selected disabled>请选择活动</option>
      <option 
      v-for="(lesson, id) in selected_lessons_name" 
      :key="id"
      :value="selected_lessons_id[id]">{{ lesson }}</option>
    </select>
  </div>
</template>

<script>
import { getAllLessons, getLessonUsers } from '@/api/lesson';

export default {
  data: () => ({
    lessons: [],
    selected_id: -1,
    selected_session: -1,
    selected_lessons: [],
    selected_lessons_name: [],
    selected_lessons_id: [],
    sessions: [[1,2,3], [4,5], [6,7], [4,5,6,7]],
    titles: ['第 1 - 3 节', '第 4 - 5 节', '第 6 - 7 节', '第 4 - 7 节'],
  }),
  mounted() {
    getAllLessons().then(({data}) => {
      this.lessons = data;
    })
  },
  methods: {

  },
  watch: {
    selected_session(val) {
      let ss = JSON.stringify(this.sessions[val]);
      this.selected_lessons = this.lessons.filter(el => JSON.stringify(el.period) == ss);
      this.selected_lessons_name = this.selected_lessons.map(el => el.name);
      this.selected_lessons_id = this.selected_lessons.map(el => el.id);
    }
  }
}
</script>

<style lang="scss">

</style>