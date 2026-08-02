<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../stores/auth";
import { useDocumentRequestStore } from "../stores/documentRequests";
import api from "../api/axios";

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();
const documentRequests = useDocumentRequestStore();

const loading = ref(true);
const error = ref("");

// Decide form state (penguji)
const decision = ref("approved");
const decisionNote = ref("");
const decisionLoading = ref(false);
const decisionError = ref("");

// Resubmit form state (pemohon)
const resubmitType = ref("");
const resubmitNote = ref("");
const resubmitLoading = ref(false);
const resubmitError = ref("");

// File upload state
const selectedFile = ref(null);
const uploadLoading = ref(false);
const uploadError = ref("");
const deletingFileId = ref(null);

const requestId = route.params.id;

async function loadRequest() {
    loading.value = true;
    error.value = "";
    try {
        await documentRequests.fetchOne(requestId);
    } catch (e) {
        error.value = "Failed to load document request";
    } finally {
        loading.value = false;
    }
}

const isOwner = computed(() => {
    return documentRequests.currentRequest?.applicant?.user_id === auth.user?.id;
});

const canDecide = computed(() => {
    return auth.hasRole("penguji") && documentRequests.currentRequest?.status === "submitted";
});

const canResubmit = computed(() => {
    return isOwner.value && documentRequests.currentRequest?.status === "revision";
});

const canUpload = computed(() => {
    return isOwner.value && documentRequests.currentRequest?.status === "revision";
});

async function handleDecide() {
    decisionError.value = "";
    decisionLoading.value = true;
    try {
        await documentRequests.decide(requestId, decision.value, decisionNote.value);
        await loadRequest();
        decisionNote.value = "";
    } catch (e) {
        decisionError.value = e.response?.data?.message || "Failed to submit decision";
    } finally {
        decisionLoading.value = false;
    }
}

async function handleResubmit() {
    resubmitError.value = "";
    resubmitLoading.value = true;
    try {
        await documentRequests.resubmit(requestId, {
            document_type: resubmitType.value || documentRequests.currentRequest.document_type,
            note: resubmitNote.value,
        });
        await loadRequest();
        resubmitNote.value = "";
    } catch (e) {
        resubmitError.value = e.response?.data?.message || "Failed to resubmit";
    } finally {
        resubmitLoading.value = false;
    }
}

function handleFileChange(event) {
    selectedFile.value = event.target.files[0];
}

async function handleUpload() {
    if (!selectedFile.value) return;
    uploadError.value = "";
    uploadLoading.value = true;
    try {
        const formData = new FormData();
        formData.append("file", selectedFile.value);
        await api.post(`/document-requests/${requestId}/files`, formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        await loadRequest();
        selectedFile.value = null;
    } catch (e) {
        uploadError.value = e.response?.data?.message || "Failed to upload file";
    } finally {
        uploadLoading.value = false;
    }
}

async function handleDeleteFile(fileId) {
    deletingFileId.value = fileId;
    try {
        await api.delete(`/document-files/${fileId}`);
        await loadRequest();
    } catch (e) {
        error.value = "Failed to delete file";
    } finally {
        deletingFileId.value = null;
    }
}

onMounted(loadRequest);
</script>

<template>
    <div class="max-w-3xl mx-auto px-6 py-10">
        <router-link
            to="/document-requests"
            class="text-sm text-blue-400 hover:text-blue-300"
        >
            &larr; Back to list
        </router-link>

        <p v-if="loading" class="text-slate-400 mt-4">Loading...</p>
        <p v-else-if="error" class="text-red-400 mt-4">{{ error }}</p>

        <div v-else-if="documentRequests.currentRequest" class="mt-4 space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-white">
                    Request #{{ documentRequests.currentRequest.id }}
                </h1>
                <span
                    class="inline-block px-3 py-1 rounded-full text-xs font-semibold capitalize"
                    :class="{
                        'bg-blue-900 text-blue-300': documentRequests.currentRequest.status === 'submitted',
                        'bg-yellow-900 text-yellow-300': documentRequests.currentRequest.status === 'revision',
                        'bg-green-900 text-green-300': documentRequests.currentRequest.status === 'approved',
                        'bg-red-900 text-red-300': documentRequests.currentRequest.status === 'rejected',
                    }"
                >
                    {{ documentRequests.currentRequest.status }}
                </span>
            </div>

            <!-- Info -->
            <section class="bg-slate-800 border border-slate-700 rounded-xl p-6 space-y-2 text-sm">
                <p><span class="text-slate-400">Document Type:</span> <span class="text-slate-200">{{ documentRequests.currentRequest.document_type }}</span></p>
                <p><span class="text-slate-400">Applicant:</span> <span class="text-slate-200">{{ documentRequests.currentRequest.applicant?.company_name }}</span></p>
                <p><span class="text-slate-400">Submitted At:</span> <span class="text-slate-200">{{ new Date(documentRequests.currentRequest.submitted_at).toLocaleString() }}</span></p>
                <p v-if="documentRequests.currentRequest.decided_at">
                    <span class="text-slate-400">Decided At:</span> <span class="text-slate-200">{{ new Date(documentRequests.currentRequest.decided_at).toLocaleString() }}</span>
                </p>
                <p v-if="documentRequests.currentRequest.note">
                    <span class="text-slate-400">Note:</span> <span class="text-slate-200">{{ documentRequests.currentRequest.note }}</span>
                </p>
            </section>

            <!-- Files -->
            <section class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-300 mb-3">Files</h2>
                <ul v-if="documentRequests.currentRequest.files?.length" class="space-y-2 mb-4">
                    <li
                        v-for="file in documentRequests.currentRequest.files"
                        :key="file.id"
                        class="flex items-center justify-between bg-slate-900 border border-slate-700 rounded-md px-3 py-2 text-sm"
                    >
                        <span class="text-slate-300">{{ file.original_name }} <span class="text-slate-500">({{ (file.size / 1024).toFixed(1) }} KB)</span></span>
                        <button
                            v-if="canUpload"
                            @click="handleDeleteFile(file.id)"
                            :disabled="deletingFileId === file.id"
                            class="text-red-400 hover:text-red-300 text-xs font-medium disabled:opacity-40"
                        >
                            {{ deletingFileId === file.id ? "Deleting..." : "Delete" }}
                        </button>
                    </li>
                </ul>
                <p v-else class="text-slate-500 text-sm mb-4">No files uploaded yet.</p>

                <div v-if="canUpload" class="flex items-center gap-3">
                    <input
                        type="file"
                        @change="handleFileChange"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="text-sm text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-slate-700 file:text-slate-200 file:text-xs file:font-medium hover:file:bg-slate-600 file:cursor-pointer"
                    />
                    <button
                        @click="handleUpload"
                        :disabled="uploadLoading || !selectedFile"
                        class="bg-blue-500 hover:bg-blue-600 disabled:bg-slate-600 disabled:cursor-not-allowed text-white text-xs font-medium px-3 py-1.5 rounded-md"
                    >
                        {{ uploadLoading ? "Uploading..." : "Upload File" }}
                    </button>
                </div>
                <p v-if="uploadError" class="text-red-400 text-sm mt-2">{{ uploadError }}</p>
            </section>

            <!-- History -->
            <section class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-300 mb-3">History</h2>
                <ul v-if="documentRequests.currentRequest.histories?.length" class="space-y-2">
                    <li
                        v-for="h in documentRequests.currentRequest.histories"
                        :key="h.id"
                        class="text-sm text-slate-300 border-l-2 border-slate-600 pl-3"
                    >
                        <span class="capitalize">{{ h.from_status || "—" }} → {{ h.to_status }}</span>
                        <span class="text-slate-500 text-xs ml-2">{{ new Date(h.created_at).toLocaleString() }}</span>
                        <div v-if="h.note" class="text-slate-400 text-xs mt-0.5">{{ h.note }}</div>
                    </li>
                </ul>
                <p v-else class="text-slate-500 text-sm">No history yet.</p>
            </section>

            <!-- Decide -->
            <section v-if="canDecide" class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-300 mb-3">Make a Decision</h2>
                <div class="flex flex-col gap-3">
                    <select
                        v-model="decision"
                        class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2"
                    >
                        <option value="approved">Approve</option>
                        <option value="revision">Request Revision</option>
                        <option value="rejected">Reject</option>
                    </select>
                    <textarea
                        v-model="decisionNote"
                        placeholder="Note (optional)"
                        rows="3"
                        class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 resize-none"
                    ></textarea>
                    <button
                        @click="handleDecide"
                        :disabled="decisionLoading"
                        class="bg-blue-500 hover:bg-blue-600 disabled:bg-slate-600 text-white text-sm font-medium py-2 rounded-md"
                    >
                        {{ decisionLoading ? "Submitting..." : "Submit Decision" }}
                    </button>
                    <p v-if="decisionError" class="text-red-400 text-sm">{{ decisionError }}</p>
                </div>
            </section>

            <!-- Resubmit -->
            <section v-if="canResubmit" class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-300 mb-3">Resubmit Application</h2>
                <div class="flex flex-col gap-3">
                    <input
                        v-model="resubmitType"
                        :placeholder="documentRequests.currentRequest.document_type"
                        class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2"
                    />
                    <textarea
                        v-model="resubmitNote"
                        placeholder="Note (optional)"
                        rows="3"
                        class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 resize-none"
                    ></textarea>
                    <button
                        @click="handleResubmit"
                        :disabled="resubmitLoading"
                        class="bg-blue-500 hover:bg-blue-600 disabled:bg-slate-600 text-white text-sm font-medium py-2 rounded-md"
                    >
                        {{ resubmitLoading ? "Resubmitting..." : "Resubmit" }}
                    </button>
                    <p v-if="resubmitError" class="text-red-400 text-sm">{{ resubmitError }}</p>
                </div>
            </section>
        </div>
    </div>
</template>