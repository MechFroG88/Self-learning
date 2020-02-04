<template>
  <div id="_header" class="header">
    <div class="primary">
      <h2 class="title">自主学习</h2>
      <div class="student_info">
        <div class="name">{{ cn_name }}</div>
        <div class="id">{{ id }}</div>
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
</template>

<script>
export default {
  props: ['cn_name', 'id'],
  data: () => ({
    
  })
}
</script>

<style lang="scss">

</style>