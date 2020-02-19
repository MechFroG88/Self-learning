<template>
  <div id="_maintenance">
    <article>
        <h1>系统维护中<span v-for="i in amount" :key="i">.</span></h1>
        <div>
            <p>我们的团队正在为这个网站添增功能以及修复漏洞。不久后将为您提供更良好的服务，请稍后再查看。</p>
            <p>&mdash; 吉隆坡中华独立中学</p>
        </div>
    </article>
  </div>
</template>

<script>
export default {
  mounted() {
    setInterval(() => {
      if (this.amount == 5) this.amount = 0;
      else this.amount += 1;
    }, 500);
  },
  data: () => ({
    amount: 0,
  })
}
</script>

<style lang="scss">
h1 { font-size: 50px; }
article { 
  font: 20px 'Noto Sans SC', sans-serif; 
  color: #333; 
  display: block; 
  text-align: left; 
  max-width: 650px; 
  margin: 0 auto; }
</style>