<script setup lang="ts">
import type { LaravelPaginator } from '../types';

defineProps<{
    meta: LaravelPaginator
}>();

const emit = defineEmits<{
    (e: 'change-page', page: number): void
}>();
</script>

<template>
    <div class="pagination">
        <button 
            :disabled="meta.current_page === 1" 
            @click="emit('change-page', meta.current_page - 1)"
            class="nav-btn"
        > 
            &larr; Previous
        </button>

        <div class="page-info">
            <span class="current">Page {{ meta.current_page }}</span>
            <span class="divider">of</span>
            <span class="total">{{ meta.last_page }}</span>
        </div>

        <button 
            :disabled="meta.current_page === meta.last_page" 
            @click="emit('change-page', meta.current_page + 1)"
            class="nav-btn"
        >
            Next &rarr;
        </button>
    </div>
</template>

<style scoped>
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    margin-top: 3rem;
    padding: 1rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.nav-btn {
    padding: 0.6rem 1.2rem;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.nav-btn:hover:not(:disabled) {
    background: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
}

.nav-btn:disabled {
    background: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
}

.page-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
    color: #4b5563;
}

.current {
    font-weight: 700;
    color: #111827;
}

.divider {
    color: #9ca3af;
}

.total {
    font-weight: 600;
}
</style>
