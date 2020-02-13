<template>
  <div id="_header" class="header">
    <div class="primary">
      <h1 class="title">开放学习</h1>
      <div class="student_info">
        <div class="name">{{ cn_name }}</div>
        <div class="id">{{ id }}</div>
      </div>
    </div>
    <h3 class="date">3月7日</h3>
    <div class="week columns">
      <span class="column col-2 col-xs-3">星期</span>
      <div class="days column col-10 col-xs-9">
        <div class="avatar" data-initial="一"></div>
        <div class="avatar" data-initial="二"></div>
        <div class="avatar" data-initial="三"></div>
        <div class="avatar" data-initial="四"></div>
        <div class="avatar" data-initial="五"></div>
        <div class="avatar active" data-initial="六"></div>
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