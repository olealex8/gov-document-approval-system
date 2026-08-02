<script setup>
import { ref, onMounted } from "vue";
import { useAuthStore } from "../stores/auth";
import { useDocumentRequestStore } from "../stores/documentRequests";
import api from "../api/axios";

const auth = useAuthStore();
const documentRequests = useDocumentRequestStore();

const loading = ref(true);
const error = ref("");
const statusFilter = ref("");
const currentPage = ref(1);
const pageInput = ref(1);

async function fetchRequests() {
    error.value = "";
    loading.value = true;
    try {
        await documentRequests.fetchList({
            status: statusFilter.value,
            page: currentPage.value,
        });
        console.log("fetch succeeded");
    } catch (e) {
        console.log("fetch failed", e);
        error.value = "Failed to load document requests";
    } finally {
        console.log("finally ran");
        loading.value = false;
    }
}

function changePage(page) {
    currentPage.value = page;
    fetchRequests();
}

function applyFilter() {
    currentPage.value = 1;
    fetchRequests();
}

function jumpToPage() {
    const page = Number(pageInput.value);
    if (page >= 1 && page <= documentRequests.lastPage) {
        changePage(page);
    }
}

async function handleExportExcel() {
    try {
        const response = await api.get("/export/excel", {
            responseType: "blob",
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement("a");
        link.href = url;
        link.setAttribute("download", "document-requests.xlsx");
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    } catch (e) {
        error.value = "Failed to export file";
    }
}

onMounted(fetchRequests);
</script>

<template>
    <div class="max-w-5xl mx-auto px-6 py-10">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-white">Document Requests</h1>
            <div class="flex items-center gap-3">
                <router-link
                    v-if="auth.hasRole('pemohon')"
                    to="/document-requests/create"
                    class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-md no-underline"
                >
                    + New Request
                </router-link>
                <button
                    @click="handleExportExcel"
                    class="bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium px-4 py-2 rounded-md"
                >
                    Export to Excel
                </button>
            </div>
        </div>

        <div class="mb-4">
            <select
                v-model="statusFilter"
                @change="applyFilter"
                class="bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2"
            >
                <option value="">All Statuses</option>
                <option value="submitted">Submitted</option>
                <option value="revision">Revision</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        <p v-if="loading" class="text-slate-400">Loading...</p>
        <p v-else-if="error" class="text-red-400">{{ error }}</p>
        <p
            v-else-if="documentRequests.requests.length === 0"
            class="text-slate-500"
        >
            No document requests found.
        </p>

        <div
            v-else
            class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden"
        >
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-900 text-slate-400 text-xs uppercase">
                        <th class="text-left px-4 py-3">ID</th>
                        <th class="text-left px-4 py-3">Document Type</th>
                        <th class="text-left px-4 py-3">Applicant</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3">Submitted At</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="req in documentRequests.requests"
                        :key="req.id"
                        class="border-t border-slate-700 hover:bg-slate-750"
                    >
                        <td class="px-4 py-3 text-slate-300">{{ req.id }}</td>
                        <td class="px-4 py-3 text-slate-200">
                            {{ req.document_type }}
                        </td>
                        <td class="px-4 py-3 text-slate-300">
                            {{ req.applicant?.company_name }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                                :class="{
                                    'bg-blue-900 text-blue-300':
                                        req.status === 'submitted',
                                    'bg-yellow-900 text-yellow-300':
                                        req.status === 'revision',
                                    'bg-green-900 text-green-300':
                                        req.status === 'approved',
                                    'bg-red-900 text-red-300':
                                        req.status === 'rejected',
                                }"
                            >
                                {{ req.status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">
                            {{
                                new Date(req.submitted_at).toLocaleDateString()
                            }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <router-link
                                :to="`/document-requests/${req.id}`"
                                class="text-blue-400 hover:text-blue-300"
                            >
                                View
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            class="flex items-center gap-2 mt-5"
            v-if="documentRequests.lastPage > 1"
        >
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

            <span class="text-sm text-slate-400 px-2">
                Page {{ currentPage }} of {{ documentRequests.lastPage }}
            </span>

            <button
                @click="changePage(currentPage + 1)"
                :disabled="currentPage === documentRequests.lastPage"
                class="w-9 h-9 rounded-md text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40"
            >
                ›
            </button>
            <button
                @click="changePage(documentRequests.lastPage)"
                :disabled="currentPage === documentRequests.lastPage"
                class="w-9 h-9 rounded-md text-sm bg-slate-800 text-slate-300 hover:bg-slate-700 disabled:opacity-40"
            >
                »
            </button>

            <form
                @submit.prevent="jumpToPage"
                class="flex items-center gap-2 ml-4"
            >
                <input
                    v-model="pageInput"
                    type="number"
                    min="1"
                    :max="documentRequests.lastPage"
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
