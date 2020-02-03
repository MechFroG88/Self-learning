<template>
  <div id="_login">
    <img class="logo img-responsive" src="@/assets/images/chkl_logo.png" alt="">
    <div class="form-container">
      <div class="form-group">
        <input class="form-input" type="text" id="student_id" 
        v-model="student_id" :class="{
          'active': student_id
        }">
        <label class="custom-form-label" for="student_id">学号</label>
      </div>
      <div class="form-group">
        <input class="form-input" type="text" id="birthday"
        v-model="birthday" :class="{
          'active': birthday
        }">
        <label class="custom-form-label" for="birthday">生日日期</label>
      </div>
    </div>
    <div class="btn btn-primary submit">登入</div>
  </div>
</template>

<script>
import { userLogin } from '@/api/user';
export default {
  data: () => ({
    student_id: '',
    birthday: ''
  }),
  methods: {
    login() {
      // userLogin({
      //   id: this.student_id,
      //   ic: this.birthday
      // }).then(({data}) => {
      //   console.log(student);
      // })
    }
  }
}
</script>

<style lang="scss">

</style>