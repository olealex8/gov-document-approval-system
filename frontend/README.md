# Sistem Persetujuan Dokumen Pemerintah — Frontend

Frontend untuk sistem pengajuan dan persetujuan dokumen kelayakan.

## Tech Stack
- Vue 3
- Vite
- Vue Router
- Pinia
- Axios
- Chart.js (vue-chartjs)

## Setup

1. Clone the repository
```bash
git clone https://gitlab.com/blaque-group/gov-doc-frontend.git
cd gov-doc-frontend
```

2. Install dependencies
```bash
npm install
```

3. Copy environment file
```bash
cp .env.example .env
```
Set `VITE_API_BASE_URL` to point to your running backend (default: `http://127.0.0.1:8000/api`)

4. Run the dev server
```bash
npm run dev
```
App available at `http://localhost:5173`
