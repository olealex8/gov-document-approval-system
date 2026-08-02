import { defineStore } from "pinia";
import api from "../api/axios";

export const useDocumentRequestStore = defineStore("documentRequests", {
    state: () => ({
        requests: [],
        currentRequest: null,
        lastPage: 1,
        loading: false,
        error: "",
    }),
    actions: {
        async fetchList({ status, page } = {}) {
            const { data } = await api.get("/document-requests", {
                params: { status: status || undefined, page: page || 1 },
            });
            console.log(data);
            this.requests = data.data;
            this.lastPage = data.meta.last_page;
        },
        async fetchOne(id) {
            const { data } = await api.get(`/document-requests/${id}`);
            this.currentRequest = data.data;
        },
        async create(payload) {
            const { data } = await api.post("/document-requests", payload);
            return data.data;
        },
        async decide(id, decision, note) {
            const { data } = await api.patch(`/document-requests/${id}/decide`, { decision, note });
            return data.data;
        },
        async resubmit(id, payload) {
            const { data } = await api.patch(`/document-requests/${id}/resubmit`, payload);
            return data.data;
        },
    },
});