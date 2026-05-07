<script setup lang="ts">
import type { Quote } from '../types';

defineProps<{
    quote: Quote
}>();
</script>

<template>
    <div class="quote-card">
        <span class="id-badge">#{{ quote.id }}</span>
        <p class="text">"{{ quote.quote }}"</p>
        <footer class="author">— {{ quote.author }}</footer>
    </div>
</template>

<style scoped>
.quote-card {
    position: relative;
    padding: 1.5rem;
    padding-left: 2rem;
    background: white;
    border-left: 5px solid #a3a3a3;
    /* Azul Laravel */
    border-radius: 0 12px 12px 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    transition: transform 0.2s;
}

.id-badge {
    font-size: 0.8rem;
    color: #9ca3af;
    position: absolute;
    top:
        0.5rem;
    right: 1rem;
}

.text {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #1f2937;
    margin: 0;
    font-style: italic;
}

.author {
    margin-top: 0.8rem;
    font-weight: 600;
    color: #4b5563;
    font-size:
        0.9rem;
}
</style>
