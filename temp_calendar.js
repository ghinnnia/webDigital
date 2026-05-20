// Extracted script from home.blade.php

// Sample data for deadline cards
const deadlineData = [
    { title: "Malaria Pendidikan", daysLeft: 2, progress: 80, status: "TERLAMBAT", statusColor: "red" },
    { title: "Vaksin Pendidikan", daysLeft: 3, progress: 60, status: "DIKERJAKAN", statusColor: "yellow" },
    { title: "Laporan Tahunan", daysLeft: 5, progress: 40, status: "DIKERJAKAN", statusColor: "blue" },
    { title: "Malaria Pendidikan", daysLeft: 7, progress: 25, status: "DIKERJAKAN", statusColor: "green" },
    { title: "Evaluasi Program", daysLeft: 10, progress: 15, status: "DIKERJAKAN", statusColor: "blue" },
    { title: "Penelitian Baru", daysLeft: 12, progress: 5, status: "RENCANA", statusColor: "gray" },
    { title: "Audit Internal", daysLeft: 14, progress: 0, status: "RENCANA", statusColor: "gray" },
    { title: "Pengembangan Kurikulum", daysLeft: 20, progress: 0, status: "RENCANA", statusColor: "gray" }
];

// Pagination variables for deadline
const itemsPerPage = 4;
let currentPage = 1;
const totalPages = Math.ceil(deadlineData.length / itemsPerPage);

// Define minimal DOM stubs to avoid DOM usage
global.document = { getElementById: () => ({ innerHTML: '', appendChild: () => {}, classList: { add: () => {} }, addEventListener: () => {}, textContent: '' }) };

document.addEventListener = () => {};

// The rest of functions are defined to check syntax only
function initializeDeadlinePagination() {
    renderDeadlineCards(currentPage);
    renderPaginationButtons();
}

function renderDeadlineCards(page) {
    const grid = document.getElementById('deadlineGrid');
    grid.innerHTML = '';

    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = Math.min(startIndex + itemsPerPage, deadlineData.length);

    for (let i = startIndex; i < endIndex; i++) {
        const deadline = deadlineData[i];
        const card = { className: '', innerHTML: '', appendChild: () => {} };

        let buttonClass = '';
        if (deadline.statusColor === 'red') {
            buttonClass = 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/70';
        } else if (deadline.statusColor === 'yellow') {
            buttonClass = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300 hover:bg-yellow-200 dark:hover:bg-yellow-900/70';
        } else if (deadline.statusColor === 'blue') {
            buttonClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-900/70';
        } else if (deadline.statusColor === 'green') {
            buttonClass = 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900/70';
        } else {
            buttonClass = 'bg-gray-100 text-gray-700 dark:bg-gray-900/50 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-900/70';
        }

        card.innerHTML = `
            <div class="image-placeholder bg-gray-200 dark:bg-gray-600 h-24 sm:h-32 rounded-md mb-3 sm:mb-4 flex items-center justify-center">
                <span class="material-icons-outlined text-gray-400 dark:text-gray-500 text-sm sm:text-base">image</span>
            </div>
            <h3 class="font-semibold text-sm mb-1">${deadline.title}</h3>
            <p class="text-xs text-text-muted-light mb-3">SISA WAKTU ${deadline.daysLeft} HARI</p>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-2">
                <div class="bg-${deadline.statusColor}-500 h-1.5 rounded-full" style="width: ${deadline.progress}%"></div>
            </div>
            <button class="mt-auto w-full text-center py-2 text-xs font-semibold ${buttonClass} rounded-md transition-colors">${deadline.status}</button>
        `;
    }
}

function renderPaginationButtons() {
    const pageNumbersContainer = document.getElementById('deadlinePageNumbers');
    for (let i = 1; i <= totalPages; i++) {
        const pageNumber = { textContent: '', className: '', addEventListener: () => {} };
        pageNumber.textContent = i;
        pageNumber.className = `desktop-page-btn ${i === currentPage ? 'active' : '' }`;
    }
}

// minimal run
initializeDeadlinePagination();
