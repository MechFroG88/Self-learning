<template>
  <div id="_details">
    <pre>{{lesson}}</pre>
    <form class="form-horizontal">
      <div class="form-group">
        <div class="col-3 col-sm-12">
          <label class="form-label" for="name">活动名称</label>
        </div>
        <div class="col-9 col-sm-12">
          <input class="form-input" type="text" id="name" placeholder="活动名称">
        </div>
      </div>
      <div class="form-group">
        <div class="col-3 col-sm-12">
          <label class="form-label" for="location">活动地点</label>
        </div>
        <div class="col-9 col-sm-12">
          <input class="form-input" type="text" id="location" placeholder="活动地点">
        </div>
      </div>
      <div class="form-group">
        <div class="col-3 col-sm-12">
          <label class="form-label" for="subject">负责单位</label>
        </div>
        <div class="col-9 col-sm-12">
          <input class="form-input" type="text" id="subject" placeholder="负责单位">
        </div>
      </div>
      <div class="form-group">
        <div class="col-3 col-sm-12">
          <label class="form-label" for="name">活动名称</label>
        </div>
        <div class="col-9 col-sm-12">
          <input class="form-input" type="text" id="name" placeholder="活动名称">
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  data: () => ({

  }),
  computed: {
    ...mapState('admin_edit', {
      lesson: 'lesson'
    }),
  }
}
</script>

<style lang="scss">

</style>