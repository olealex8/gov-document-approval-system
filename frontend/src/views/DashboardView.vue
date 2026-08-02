<script setup>
import { ref, onMounted } from "vue";
import { Doughnut, Bar } from "vue-chartjs";
import {
    Chart as ChartJS,
    ArcElement,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from "chart.js";
import api from "../api/axios";

ChartJS.register(ArcElement, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

const loading = ref(true);
const error = ref("");
const summary = ref(null);

async function loadSummary() {
    loading.value = true;
    error.value = "";
    try {
        const { data } = await api.get("/dashboard/summary");
        summary.value = data;
    } catch (e) {
        error.value = "Failed to load dashboard";
    } finally {
        loading.value = false;
    }
}

const statusColors = {
    submitted: "#60a5fa",
    revision: "#facc15",
    approved: "#4ade80",
    rejected: "#f87171",
};

function statusChartData(byStatus) {
    const labels = Object.keys(byStatus);
    return {
        labels,
        datasets: [
            {
                data: Object.values(byStatus),
                backgroundColor: labels.map((l) => statusColors[l] || "#94a3b8"),
                borderWidth: 0,
            },
        ],
    };
}

function trendChartData(recent) {
    return {
        labels: recent.map((r) => r.date),
        datasets: [
            {
                label: "Submissions",
                data: recent.map((r) => r.count),
                backgroundColor: "#3b82f6",
                borderRadius: 4,
            },
        ],
    };
}

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: { color: "#cbd5e1" },
        },
    },
};

const barOptions = {
    ...chartOptions,
    scales: {
        x: { ticks: { color: "#94a3b8" }, grid: { color: "#334155" } },
        y: { ticks: { color: "#94a3b8" }, grid: { color: "#334155" } },
    },
};

onMounted(loadSummary);
</script>

<template>
    <div class="max-w-5xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold text-white mb-8">Dashboard</h1>

        <p v-if="loading" class="text-slate-400">Loading...</p>
        <p v-else-if="error" class="text-red-400">{{ error }}</p>

        <div v-else-if="summary" class="space-y-8">
            <!-- Stat cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-4">
                    <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">Total</p>
                    <p class="text-2xl font-bold text-white">{{ summary.total }}</p>
                </div>
                <div
                    v-for="(count, status) in summary.by_status"
                    :key="status"
                    class="bg-slate-800 border border-slate-700 rounded-xl p-4"
                >
                    <p class="text-xs uppercase tracking-wide text-slate-400 mb-1">{{ status }}</p>
                    <p class="text-2xl font-bold" :style="{ color: statusColors[status] }">{{ count }}</p>
                </div>
            </div>

            <!-- Charts -->
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                    <h2 class="text-sm font-semibold text-slate-300 mb-4">Status Breakdown</h2>
                    <div class="h-64" v-if="Object.keys(summary.by_status).length">
                        <Doughnut :data="statusChartData(summary.by_status)" :options="chartOptions" />
                    </div>
                    <p v-else class="text-slate-500 text-sm">No data yet.</p>
                </div>

                <div class="bg-slate-800 border border-slate-700 rounded-xl p-6">
                    <h2 class="text-sm font-semibold text-slate-300 mb-4">Last 30 Days</h2>
                    <div class="h-64" v-if="summary.recent_30_days.length">
                        <Bar :data="trendChartData(summary.recent_30_days)" :options="barOptions" />
                    </div>
                    <p v-else class="text-slate-500 text-sm">No submissions in the last 30 days.</p>
                </div>
            </div>
        </div>
    </div>
</template>