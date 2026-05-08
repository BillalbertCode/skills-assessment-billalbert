<script setup lang="ts">
import { onMounted, ref } from 'vue';
import type { LaravelPaginator, Quote } from './types';
import QuoteCard from './components/QuoteCard.vue';
import QuoteSearch from './components/QuoteSearch.vue';
import QuotePagination from './components/QuotePagination.vue';

const quotes = ref<Quote[]>([]);
const searchResult = ref<Quote | null>(null);
const pagination = ref<LaravelPaginator | null>(null);
const isLoading = ref(false);
const highlightedId = ref<number | null>(null);
const currentPage = ref(1);

const loadPage = async (page = 1) => {
    isLoading.value = true;
    searchResult.value = null;
    highlightedId.value = null;
    currentPage.value = page;

    try {
        const response = await fetch(`/api/quotes?page=${page}`);
        const result: LaravelPaginator = await response.json();

        quotes.value = result.data;
        pagination.value = result;
    } catch (error) {
        console.error("Error loading quotes:", error);
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

        // Always refresh the current page from the backend cache.
        await loadPage(currentPage.value);

        // Now set the search result to show the focus
        searchResult.value = result;

        // Trigger highlight animation
        highlightedId.value = result.id;
        setTimeout(() => {
            highlightedId.value = null;
        }, 3000);

    } catch {
        alert("Quote not found!");
    } finally {
        isLoading.value = false;
    }
};

const clearSearch = () => {
    searchResult.value = null;
    highlightedId.value = null;
};

onMounted(() => loadPage());
</script>

<template>
    <div class="app-wrapper">
        <header class="main-header">
            <h1>Quotes Explorer</h1>
            <p>Discover and search for inspirational quotes</p>
        </header>

        <main class="container">
            <QuoteSearch @search="handleSearch" />

            <div v-if="isLoading" class="loading-state">
                Processing...
            </div>

            <div v-else class="list-container">
                <!-- Search Result Focus -->
                <div v-if="searchResult" class="search-highlight">
                    <div class="search-header">
                        <h3>Focused Quote:</h3>
                        <button class="clear-btn" @click="clearSearch">Clear Focus</button>
                    </div>
                    <QuoteCard :quote="searchResult" />
                    <hr class="separator">
                </div>

                <!-- Main Quotes Table -->
                <section class="table-section">
                    <h3 class="section-title">
                        {{ searchResult ? 'Quotes in Context:' : 'Inspirational Quotes' }}
                    </h3>
                    
                    <div class="table-responsive">
                        <table class="quotes-table">
                            <thead>
                                <tr>
                                    <th class="col-id">ID</th>
                                    <th class="col-quote">Quote</th>
                                    <th class="col-author">Author</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="q in quotes" 
                                    :key="q.id"
                                    :class="{ 'row-highlight': highlightedId === q.id }"
                                >
                                    <td class="col-id">#{{ q.id }}</td>
                                    <td class="col-quote">
                                        "{{ q.quote }}"
                                        <span v-if="highlightedId === q.id" class="new-badge">JUST FOUND</span>
                                    </td>
                                    <td class="col-author">{{ q.author }}</td>
                                </tr>
                                <tr v-if="quotes.length === 0 && !searchResult">
                                    <td colspan="3" class="empty-row">
                                        No quotes found. Try importing some via the CLI.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <!-- Pagination Controls -->
            <QuotePagination 
                v-if="pagination && pagination.last_page > 1" 
                :meta="pagination" 
                @change-page="loadPage" 
            />
        </main>
    </div>
</template>

<style>
body {
    background: #f9fafb;
    margin: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
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
    font-weight: 800;
    letter-spacing: -0.025em;
}

.main-header p {
    color: #6b7280;
    margin-top: 0.5rem;
    font-size: 1.125rem;
}

.container {
    max-width: 900px;
    margin: 0 auto;
}

.loading-state {
    text-align: center;
    padding: 3rem;
    color: #808080;
    font-weight: bold;
    font-size: 1.25rem;
}

.search-highlight {
    background: #f5f5f5;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2.5rem;
    border: 1px solid #d3d3d3;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
}

.search-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.search-header h3 {
    margin: 0;
    color: #404040;
    font-size: 1.1rem;
    font-weight: 700;
}

.clear-btn {
    background: #a9a9a9;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    transition: background 0.2s;
}

.clear-btn:hover {
    background: #696969;
}

.separator {
    border: 0;
    border-top: 1px solid #d3d3d3;
    margin: 1.5rem 0;
}

.section-title {
    color: #404040;
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
    font-weight: 700;
}

/* Table Styles */
.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    border: 1px solid #e0e0e0;
}

.quotes-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

.quotes-table th {
    background: #f9f9f9;
    padding: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #808080;
    border-bottom: 1px solid #e0e0e0;
}

.quotes-table td {
    padding: 1.25rem 1rem;
    font-size: 0.95rem;
    color: #404040;
    border-bottom: 1px solid #f5f5f5;
    vertical-align: top;
    transition: background-color 0.5s ease;
}

/* Animation for the "Optimistic" row */
.row-highlight td {
    background-color: #e0e0e0 !important; /* Soft gray */
    border-bottom-color: #d3d3d3;
}

.new-badge {
    display: inline-block;
    padding: 0.1rem 0.4rem;
    background: #d3d3d3;
    color: #404040;
    font-size: 0.65rem;
    font-weight: 800;
    border-radius: 4px;
    margin-left: 0.5rem;
    vertical-align: middle;
}

.quotes-table tr:last-child td {
    border-bottom: none;
}

.quotes-table tr:hover td:not(.row-highlight td) {
    background-color: #f9f9f9;
}

.col-id {
    width: 80px;
    color: #808080;
    font-weight: 600;
}

.col-quote {
    font-style: italic;
    line-height: 1.5;
}

.col-author {
    width: 180px;
    font-weight: 600;
    color: #696969;
}

.empty-row {
    text-align: center;
    padding: 3rem !important;
    color: #a9a9a9;
}
</style>
