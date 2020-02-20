<template>
  <div id="_edit_user">
    <user-table ref="table" title hoverable
    navbar="学生学号"
    :columns="user_columns"
    :tableData="table_data">
      <template slot="title">
        学生列表
      </template>
      <template slot="action" slot-scope="{ data }">
        <div class="btn"
        @click="open(data)">更改密码</div>
      </template>
    </user-table>

    <modal ref="modal" title="更改登入密码">
      <div slot="body">
        <validation-provider v-slot="{ errors }" ref="ic"
        :rules="{
          required: true,
          regex: /^(?:[0-9]{6}[-]?[0-9]{2}[-]?[0-9]{4}|[0-9a-zA-Z]{0,12}|[0-9]{2}\/[0-9]{2}\/[0-9]{4})$/
        }">
          <div class="form-group">
            <label for="ic" class="form-label">新IC号码/新密码</label>
            <input type="text" class="form-input" :class="{'is-error': errors.length}"
            id="ic" placeholder="IC或生日日期" v-model="new_pass">
          </div>
          <p class="text-error form-input-hint">{{ errors[0] }}</p>
        </validation-provider>
      </div>
      <div slot="footer">
        <div class="btn btn-primary" style="float: right;" @click="submit">确定</div>
      </div>
    </modal>
  </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex';
import { getAllUsers, changeUserPassword } from '@/api/user';

import modal from '@/components/modal';
import userTable from '@/components/table';

import { user_columns } from '@/api/tableColumns';

export default {
  components: {
    userTable,
    modal,
  },
  mounted() {
    this.$nextTick(() => this.table_data = this.users);
  },
  data: () => ({
    user_columns,
    table_data: [],
    new_pass: '',
    isSubmitLoading: false,
    selected_user: {},
  }),
  methods: {
    ...mapMutations('admin_data', {
      setUsers: 'SET_USERS'
    }),
    open(user) {
      this.$refs.modal.active = true;
      this.selected_user = user;
    },
    async submit() {
      let validIc = await this.$refs.ic.validate();
      if (validIc.valid) {
        this.isLoading = true;
        let icArr = this.new_pass.split("").filter(el => el != '-');
        if (/^[0-9]{12}$/.test(icArr.join(''))) {
          icArr.splice(8, 0, '-');
          icArr.splice(6, 0, '-');
        }
        this.isSubmitLoading = true;
        changeUserPassword(this.selected_user.id, {ic: icArr.join('')})
          .then((data) => {
            if (data.status == 200) {
              this.$refs.modal.active = false;
              this.$refs.table.is_loading = true;
              getAllUsers()
                .then(({data}) => this.setUsers(data))
                .then(() => this.table_data = this.users);
            }
          })
          .catch((err) => {
            this.$notify({
              type: 'error',
              title: '密码更新发生错误，请稍后重试。'
            });
          })
          .finally(() => {
            this.isSubmitLoading = false;
          })
      }
    }
  },
  computed: {
    ...mapState('admin_data', {
      users: 'users'
    }),
  }
}
</script>

<style lang="scss">

</style>