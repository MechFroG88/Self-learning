<template>
  <div id="_cards" class="tile tile-centered" :class="{active}">
    <div class="tile-icon" @click="action">
      <figure class="avatar avatar-lg" :data-initial="initials"
      :style="{'background-color': bgColor || ''}"></figure>
    </div>
    <div class="tile-content" @click="action">
      <div class="tile-title">{{ title }}</div>
      <small class="tile-subtitle">
        <div class="pax mr-2">
          <div>人数：{{num}}/{{pax}}</div>
          <div class="bar bar-sm">
            <div class="bar-item" role="progressbar" :style="{
              'width': `${num*100/pax}%`,
              'background-color': num*100/pax > 85 ? '#e85600' : num*100/pax > 60 ? '#ffb700': '#32b643'
            }"
            aria-valuemin="0" :aria-valuemax="pax" :aria-valuenow="num"></div>
          </div>
        </div>
        <div class="class ml-2">{{ classroom }}</div>
      </small>
    </div>
    <div class="tile-action">
      <button class="btn btn-link" @click="btnAction">
        <i class="icon icon-more-vert"></i>
      </button>
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
    active: {
      type: Boolean,
      default: false
    },
    disable: {
      type: Boolean,
      default: false,
    },
    disableMsg: {
      type: String
    }
  },
  data: () => ({
    
  }),
  methods: {
    action() {
      if (!this.disable) this.$emit('clicked');
      else this.$notify({
        type: 'warn',
        title: this.disableMsg
      });
    },
    btnAction() {
      this.$emit('action');
    }
  },
}
</script>

<style lang="scss">

</style>