<template>
  <div id="_login">
    <img class="logo img-responsive" src="@/assets/images/chkl_logo.png" alt="">
    <div class="form-container">
      <div class="form-group">
        <input class="form-input" type="text" id="student_id" 
        style="letter-spacing: 2px;"
        v-model="student_id" :class="{
          'active': student_id
        }">
        <label class="custom-form-label" for="student_id">学号</label>
      </div>
      <div class="form-group">
        <input class="form-input" type="password" id="birthday"
        style="letter-spacing: 3px;"
        v-model="birthday" :class="{
          'active': birthday
        }">
        <label class="custom-form-label" for="birthday">生日日期</label>
      </div>
    </div>
    <div class="btn btn-primary submit loading-lg" 
    :class="{'loading': isLoading}"
    @click="login">登入</div>
  </div>
</template>

<script>
import { userLogin } from '@/api/user';
export default {
  data: () => ({
    student_id: '',
    birthday: '',
    isLoading: false,
  }),
  methods: {
    login() {
      this.isLoading = true;
      userLogin({
        id: this.student_id,
        ic: this.birthday
      }).then((data) => {
        console.log(data)
        if (data.status == 200) {
          this.$router.push('/home');
        }
      }).finally(() => this.isLoading = false)
    }
  }
}
</script>

<style lang="scss">

</style>