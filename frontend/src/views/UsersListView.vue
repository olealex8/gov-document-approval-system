<script setup>
import { ref, onMounted } from "vue";
import api from "../api/axios";

const users = ref([]);
const loading = ref(true);
const error = ref("");
const currentPage = ref(1);
const lastPage = ref(1);
const pageInput = ref(1);

async function loadUsers() {
    loading.value = true;
    error.value = "";
    try {
        const { data } = await api.get("/users", { params: { page: currentPage.value } });
        console.log(data);
        users.value = data.data;
        lastPage.value = data.meta.last_page;
    } catch (e) {
        console.error(e);
        error.value = "Failed to load users";
    } finally {
        loading.value = false;
    }
}

function changePage(page) {
    currentPage.value = page;
    loadUsers();
}

function jumpToPage() {
    const page = Number(pageInput.value);
    if (page >= 1 && page <= lastPage.value) {
        changePage(page);
    }
}

onMounted(loadUsers);
</script>

<template>
    <div class="max-w-5xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold text-white mb-6">Users</h1>

        <p v-if="loading" class="text-slate-400">Loading...</p>
        <p v-else-if="error" class="text-red-400">{{ error }}</p>

        <div v-else class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-900 text-slate-400 text-xs uppercase">
                        <th class="text-left px-4 py-3">ID</th>
                        <th class="text-left px-4 py-3">Name</th>
                        <th class="text-left px-4 py-3">Email</th>
                        <th class="text-left px-4 py-3">Role</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id" class="border-t border-slate-700">
                        <td class="px-4 py-3 text-slate-300">{{ user.id }}</td>
                        <td class="px-4 py-3 text-slate-200">{{ user.name }}</td>
                        <td class="px-4 py-3 text-slate-300">{{ user.email }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                                :class="
                                    user.roles.some(r => r.name === 'penguji')
                                        ? 'bg-purple-900 text-purple-300'
                                        : 'bg-slate-700 text-slate-300'
                                "
                            >
                                {{ user.roles.map(r => r.name).join(', ') }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center gap-2 mt-5" v-if="lastPage > 1">
            <button
                @click="changePage(1)"
                :disabled="currentPage === 1"
                class="w-9 h-9 rounded-md text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40"
            >
                «
            </button>
            <button
                @click="changePage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="w-9 h-9 rounded-md text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40"
            >
                ‹
            </button>

            <span class="text-sm text-slate-400 px-2">Page {{ currentPage }} of {{ lastPage }}</span>

            <button
                @click="changePage(currentPage + 1)"
                :disabled="currentPage === lastPage"
                class="w-9 h-9 rounded-md text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40"
            >
                ›
            </button>
            <button
                @click="changePage(lastPage)"
                :disabled="currentPage === lastPage"
                class="w-9 h-9 rounded-md text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40"
            >
                »
            </button>

            <form @submit.prevent="jumpToPage" class="flex items-center gap-2 ml-4">
                <input
                    v-model="pageInput"
                    type="number"
                    min="1"
                    :max="lastPage"
                    class="w-20 bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-md px-2 py-1.5"
                />
                <button
                    type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-3 py-1.5 rounded-md"
                >
                    Go
                </button>
            </form>
        </div>
    </div>
</template>