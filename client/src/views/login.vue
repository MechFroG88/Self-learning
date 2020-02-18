<template>
  <div id="_login">
    <div class="logo">
      <h1 class="cn">开放学习日</h1>
      <h4 class="en">Open Learning Day</h4>
      <!-- <img class="img img-responsive" src="@/assets/images/chkl_logo.png" alt=""> -->
    </div>
    <form @submit.prevent="login" method="POST" action="/user/login">
      <div class="form-container">
        <div class="toast toast-error" v-if="formError || loginError">
          <button class="btn btn-clear float-right" @click="reset"></button>
          {{ formError ? '表格填写错误，请重新输入' : '登入失败，学号或身份证输入错误' }}
        </div>
        <validation-provider v-slot="{ errors }" rules="required|numeric" ref="id">
          <div class="form-group">
            <input class="form-input" type="text" id="student_id" 
            style="letter-spacing: 2px;" name="学号"
            v-model="student_id" :class="{
              'active': student_id,
              'error': errors.length
            }">
            <label class="custom-form-label" for="student_id">学号</label>
          </div>
          <p class="text-error form-input-hint">{{ errors[0] }}</p>
        </validation-provider>
        <validation-provider v-slot="{ errors }" ref="ic"
        :rules="{
          required: true,
          regex: /^(?:[0-9]{6}[-]?[0-9]{2}[-]?[0-9]{4}|[0-9a-zA-Z]{0,12}|[0-9]{2}\/[0-9]{2}\/[0-9]{4})$/
        }">
          <div class="form-group">
            <input class="form-input" type="password" id="student_ic"
            style="letter-spacing: 3px;" name="身份证/护照号码"
            placeholder="外籍生请填写护照号码"
            v-model="student_ic" :class="{
              'active': student_ic,
              'error': errors.length
            }">
            <label class="custom-form-label" for="student_ic">IC 号码</label>
          </div>
          <p class="text-error form-input-hint">{{ errors[0] }}</p>
        </validation-provider>
      </div>
      <button class="btn btn-primary submit loading-lg" type="submit"
      :class="{'loading': isLoading}" style="width: 100%">
        登入
      </button>
    </form>
  </div>
</template>

<script>
import { userLogin, getUser } from '@/api/user';
import { mapMutations } from 'vuex';

export default {
  data: () => ({
    student_id: '',
    student_ic: '',
    isLoading: false,
    formError: false,
    loginError: false,
  }),
  methods: {
    ...mapMutations('/lesson', {
      reset: 'RESET'
    }),
    async login() {
      let validId = await this.$refs.id.validate();
      let validIc = await this.$refs.ic.validate();
      if (validId.valid && validIc.valid) {
        this.isLoading = true;
        let icArr = this.student_ic.split("").filter(el => el != '-');
        if (icArr.length > 9) {
          icArr.splice(8, 0, '-');
          icArr.splice(6, 0, '-');
        }
        userLogin({
          id: this.student_id,
          ic: icArr.join('')
        }).then((data) => {
          if (data.status == 200) {
            this.reset();
            getUser().then(({data}) => {
              if (data.type == 1) this.$router.push('/home');
              else this.$router.push('/admin');
            }).catch((err) => {
              console.log(err);
            });
          }
        })
        .catch((err) => {
          if (err.response.status == 400) {
            this.formError = true;
          }
          else if (err.response.status == 401) {
            this.loginError = true;
          }
          this.$refs.id.reset();
          this.$refs.ic.reset();
        })
        .finally(() => this.isLoading = false)
      }
    },
    reset() {
      this.loginError = false;
      this.formError = false;
    }
  },
}
</script>

<style lang="scss">

</style>