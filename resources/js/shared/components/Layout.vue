<script setup>
import NavBar from "./NavBar.vue";
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <NavBar />
        <main class="max-w-6xl mx-auto px-6 py-8">
            <slot />
        </main>
    </div>
</template>
