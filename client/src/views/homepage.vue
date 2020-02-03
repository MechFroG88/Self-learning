<template>
  <div id="_homepage">
    <div class="header">
      <div class="primary">
        <h2 class="title">自主学习</h2>
        <div class="student_info">
          <div class="name">黄筱琪</div>
          <div class="id">170425</div>
        </div>
      </div>
      <h4 class="date">2月15日</h4>
      <div class="week columns">
        <span class="column col-2 col-xs-3">星期</span>
        <div class="days column col-10 col-xs-9">
          <div class="avatar avatar-sm" data-initial="一"></div>
          <div class="avatar avatar-sm" data-initial="二"></div>
          <div class="avatar avatar-sm" data-initial="三"></div>
          <div class="avatar avatar-sm" data-initial="四"></div>
          <div class="avatar avatar-sm" data-initial="五"></div>
          <div class="avatar avatar-sm active" data-initial="六"></div>
        </div>
      </div>
    </div>
    <div class="body columns">
      <div class="sidebar col-2 col-xs-3">
        <div class="period-counter">
          <h6 style="text-align: center; margin-bottom: 1rem;">节数</h6>
          <div class="cell" v-for="i in 7" :key="i" 
          :class="{'active': i < 4}">
            <div class="period">{{i}}</div>
            <div class="progress-bar"></div>
          </div>
        </div>
        <div class="logout">
          <i class="feather icon-log-out"></i>登出
        </div>
      </div>
      <div class="content column col-10 col-xs-9">
        <div class="accordion" v-for="i in arr" :key="i">
          <input type="radio" :id="i" name="accordion-checkbox" hidden>
          <label class="accordion-header" :for="i">
            <div class="accordion-header-section">
              <i class="icon icon-arrow-right mr-1"></i>
              {{i}}
            </div>
            <small class="label label-rounded label-primary">已选：活动1</small>
          </label>
          <div class="accordion-body">
            <card
            title="活动标题"
            initials="华"
            :pax="800"
            :num="351"
            classroom="4A Ren"
            bg-color="#ffdf76"></card>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import card from '../components/card';

export default {
  components: {
    card
  },
  data: () => ({
    arr: ['hi', 'hihi', 'hihihi']
  }),
}
</script>

<style lang="scss">

</style>