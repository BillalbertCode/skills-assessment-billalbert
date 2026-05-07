<script setup lang="ts">
import { ref } from 'vue';

const searchId = ref('');
const emit = defineEmits<{
    (e: 'search', id: number): void
}>();

const handleSearch = () => {
    const id = parseInt(searchId.value);
    if (!isNaN(id)) {
        emit('search', id);
    }
};
</script>

<template>
    <div class="search-container">
        <input v-model="searchId" type="number" placeholder=" ID(eg: 50)..." @keyup.enter="handleSearch">
        <button @click="handleSearch">Search Quote</button>
    </div>
</template>

<style scoped>
.search-container {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
}

input {
    flex: 1;
    padding: 0.8rem;
    border: 2px solid #cbd5e1;
    border-radius: 8px;
    background: #f8fafc;
    color: #334155;
    font-size: 1rem;
    transition: border-color 0.2s, background-color 0.2s;
}

input:focus {
    outline: none;
    border-color: #94a3b8;
    background: #eff2f7;
}

button {
    padding: 0.8rem 1.5rem;
    background: #64748b;
    color: #f8fafc;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.2s, transform 0.2s;
}

button:hover {
    background: #475569;
    transform: translateY(-1px);
}
</style>
