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
    background: #f3f4f6;
    border-radius: 12px;
    border: 1px solid #d1d5db;
    box-shadow: 0 1px 2px 0 rgba(15, 23, 42, 0.05);
}

.nav-btn {
    padding: 0.6rem 1.2rem;
    background: #9ca3af;
    color: #111827;
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
    background: #6b7280;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(17, 24, 39, 0.1);
}

.nav-btn:disabled {
    background: #e5e7eb;
    color: #6b7280;
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
    color: #1f2937;
}

.divider {
    color: #6b7280;
}

.total {
    font-weight: 600;
    color: #374151;
}
</style>
