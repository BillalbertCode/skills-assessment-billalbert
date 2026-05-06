<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { PaginationMeta, Quote, QuoteResponse } from './types';
import QuoteCard from './components/QuoteCard.vue';
import QuoteSearch from './components/QuoteSearch.vue';
import QuotePagination from './components/QuotePagination.vue';

const quotes = ref<Quote[]>([]);
const meta = ref<PaginationMeta | null>(null);
const isLoading = ref(false);

const loadPage = async (page = 1) => {
    isLoading.value = true;
    try {
        const response = await fetch(`/api/quotes?page=${page}`);
        const result: QuoteResponse = await response.json();
        quotes.value = result.data;
        meta.value = result.meta;
    } finally {
        isLoading.value = false;
    }
};

const handleSearch = async (id: number) => {
    isLoading.value = true;
    try {
        const response = await fetch(`/api/quotes/${id}`);
        if (!response.ok) throw new Error();

        const result: Quote = await response.json();
        quotes.value = [result]; 
        meta.value = null; 
    } catch {
        alert("¡Not found!");
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => loadPage());
</script>

<template>
    <div class="app-wrapper">
        <header class="main-header">
            <h1>Quotes Explorer</h1>
            <p>Searching </p>
        </header>

        <main class="container">
            <QuoteSearch @search="handleSearch" />

            <div v-if="isLoading" class="loading-state">
                Loading...
            </div>

            <div v-else class="list-container">
                <QuoteCard v-for="q in quotes" :key="q.id" :quote="q" />

                <div v-if="quotes.length === 0" class="empty-state">
                    There are no quotes to display. Try importing some.
                </div>
            </div>

            <QuotePagination v-if="meta" :meta="meta" @change-page="loadPage" />
        </main>
    </div>
</template>

<style>
body {
    background: #f9fafb;
    margin: 0;
    font-family: 'Inter', sans-serif;
}

.app-wrapper {
    min-height: 100vh;
    padding: 2rem 1rem;
}

.main-header {
    text-align: center;
    margin-bottom: 3rem;
}

.main-header h1 {
    color: #111827;
    font-size: 2.5rem;
    margin: 0;
}

.main-header p {
    color: #6b7280;
    margin-top: 0.5rem;
}

.container {
    max-width: 600px;
    margin: 0 auto;
}

.loading-state {
    text-align: center;
    padding: 3rem;
    color: #4f46e5;
    font-weight: bold;
}
</style>
