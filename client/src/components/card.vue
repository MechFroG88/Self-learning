<template>
  <div id="_cards" class="tile tile-centered">
    <div class="tile-icon">
      <figure class="avatar avatar-lg" :data-initial="initials"
      :style="{'background-color': bgColor || ''}"></figure>
    </div>
    <div class="tile-content">
      <div class="tile-title">{{ title }}</div>
      <small class="tile-subtitle">
        <div class="pax">
          <div>人数：{{num}}/{{pax}}</div>
          <div class="bar bar-sm">
            <div class="bar-item" role="progressbar" :style="{'width': `${num*100/pax}%`}"
            aria-valuemin="0" :aria-valuemax="pax" :aria-valuenow="num"></div>
          </div>
        </div>
        <div class="class">{{ classroom }}</div>
      </small>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true
    },
    bgColor: {
      type: String
    },
    initials: {
      type: String,
      default: "-",
      validator: (val) => {
        return val.length == 1
      }
    },
    pax: {
      type: Number,
      required: true
    },
    num: {
      type: Number,
      required: true
    },
    classroom: {
      type: String,
      required: true
    },
  },
  data: () => ({
    
  })
}
</script>

<style lang="scss">

</style>