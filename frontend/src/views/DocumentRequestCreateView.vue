<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useDocumentRequestStore } from "../stores/documentRequests";
import api from "../api/axios";

const router = useRouter();
const documentRequests = useDocumentRequestStore();

const documentType = ref("");
const note = ref("");
const selectedFile = ref(null);
const loading = ref(false);
const error = ref("");

function handleFileChange(event) {
    selectedFile.value = event.target.files[0];
}

async function handleSubmit() {
    error.value = "";
    loading.value = true;
    try {
        const created = await documentRequests.create({
            document_type: documentType.value,
            note: note.value,
        });

        if (selectedFile.value) {
            const formData = new FormData();
            formData.append("file", selectedFile.value);
            await api.post(`/document-requests/${created.id}/files`, formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });
        }

        router.push(`/document-requests/${created.id}`);
    } catch (e) {
        if (e.response?.status === 422) {
            const errors = e.response.data.errors || {};
            error.value = Object.values(errors).flat().join(" ");
        } else {
            error.value = e.response?.data?.message || "Failed to create request";
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="max-w-2xl mx-auto px-6 py-10">
        <router-link
            to="/document-requests"
            class="text-sm text-blue-400 hover:text-blue-300"
        >
            &larr; Back to list
        </router-link>

        <h1 class="text-3xl font-bold text-white mt-4 mb-6">New Document Request</h1>

        <div class="bg-slate-800 border border-slate-700 rounded-xl p-8">
            <form @submit.prevent="handleSubmit" class="flex flex-col gap-5">
                <label class="flex flex-col gap-1.5 text-sm text-slate-300">
                    Document Type
                    <input
                        v-model="documentType"
                        placeholder="e.g. SIUP, IMB"
                        required
                        class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </label>

                <label class="flex flex-col gap-1.5 text-sm text-slate-300">
                    Note (optional)
                    <textarea
                        v-model="note"
                        placeholder="Any additional notes"
                        rows="4"
                        class="bg-slate-900 border border-slate-700 text-slate-200 text-sm rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                    ></textarea>
                </label>

                <label class="flex flex-col gap-1.5 text-sm text-slate-300">
                    Attach File (optional)
                    <input
                        type="file"
                        @change="handleFileChange"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="text-sm text-slate-300 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-500 file:text-white file:text-sm file:font-medium hover:file:bg-blue-600 file:cursor-pointer"
                    />
                </label>

                <button
                    type="submit"
                    :disabled="loading"
                    class="bg-blue-500 hover:bg-blue-600 disabled:bg-slate-600 disabled:cursor-not-allowed text-white text-sm font-medium py-2.5 rounded-md mt-2"
                >
                    {{ loading ? "Submitting..." : "Submit Request" }}
                </button>
            </form>

            <p v-if="error" class="text-red-400 text-sm mt-4">{{ error }}</p>
        </div>
    </div>
</template>