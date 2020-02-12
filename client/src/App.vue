<template>
  <div id="app">
    <vue-progress-bar></vue-progress-bar>
    <notifications clean position="top left" style="margin-top: 1rem;" width="50%" :max="5" />
    <router-view/>
  </div>
</template>

<style lang="scss">
#app {
  font-family: 'Noto Sans SC', sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #747474;
}
</style>
