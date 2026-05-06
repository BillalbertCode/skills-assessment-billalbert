<script setup lang="ts">
import type { PaginationMeta } from '../types';

defineProps<{
    meta: PaginationMeta
}>();

const emit = defineEmits<{
    (e: 'change-page', page: number): void
}>();
</script>

<template>
    <div class="pagination">
        <button :disabled="meta.current_page === 1" @click="emit('change-page', meta.current_page - 1)"> Anterior
        </button>

        <span class="info">Página {{ meta.current_page }} de {{ meta.last_page
            }}</span>

        <button :disabled="meta.current_page === meta.last_page" @click="emit('change-page', meta.current_page + 1)">
            Siguiente </button>
    </div>
</template>

<style scoped>
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
}

button {
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    cursor: pointer;
}

button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.info {
    font-weight: 500;
    color: #374151;
}
</style>
