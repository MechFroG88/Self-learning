<template>
  <div class="modal" :class="{active}"
  id="_modal">
    <a class="modal-overlay" aria-label="Close" @click="active = false"></a>
    <div class="modal-container">
      <div class="modal-header">
        <a class="btn btn-clear float-right" aria-label="Close" @click="active = false"></a>
        <div class="modal-title h5">{{ title }}</div>
      </div>
      <div class="modal-body">
        <div class="content">
          <slot name="body" :data="bodyData" />
        </div>
      </div>
      <div class="modal-footer">
        <slot name="footer" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['title', 'bodyData'],
  data: () => ({
    active: false,
  })
}
</script>

<style lang="scss">

</style>