<template>
  <div class="app-layout">
    <Navbar />

    <main class="app-main">
      <slot />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue'
</script>

<style scoped>
.app-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.app-main {
  flex-grow: 1;
}
</style>
