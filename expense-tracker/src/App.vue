<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />
    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import Navbar from '@/components/Shared/Navbar.vue'
</script>

<style>
/* Mobile menu transition */
.mobile-menu-enter-active,
.mobile-menu-leave-active {
  transition:
    opacity 0.3s ease,
    transform 0.3s ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

@media (max-width: 640px) {
  .nav-content {
    padding: 1rem;
  }

  .mobile-menu {
    position: fixed;
    top: 64px;
    left: 0;
    right: 0;
    background-color: white;
    padding: 1rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-top: 1px solid #e5e7eb;
    z-index: 50;
  }
}
</style>
