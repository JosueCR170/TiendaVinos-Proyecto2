<template>
  <component :is="layout">
    <RouterView />
  </component>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, RouterView } from 'vue-router'
import AppLayout from '@/layouts/AppLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

const route = useRoute()

// Resuelve el layout basado en la ruta (por defecto AppLayout, o AdminLayout si la ruta empieza con /admin)
const layout = computed(() => {
  if (route.path.startsWith('/admin')) {
    return AdminLayout
  }
  return AppLayout
})
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,400;0,700;1,400;1,700&family=Manrope:wght@400;500;600;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

*, *::before, *::after {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

:root {
  --color-primary:            #2a0002;
  --color-primary-hover:      #3d0003;
  --color-surface:            #fbfbe2;
  --color-surface-alt:        #f5f5dc;
  --color-surface-container:  #eaead1;
  --color-on-surface:         #1b1d0e;
  --color-on-surface-muted:   rgba(27, 29, 14, 0.6);
  --color-tertiary:           #735c00;
  --color-border:             rgba(218, 193, 191, 0.3);
  --font-serif:               'Noto Serif', serif;
  --font-sans:                'Manrope', sans-serif;
}

html, body {
  background-color: var(--color-surface);
  color: var(--color-on-surface);
  font-family: var(--font-sans);
  line-height: 1.6;
}

a { text-decoration: none; }
button { font-family: inherit; }
img { display: block; max-width: 100%; }
</style>
