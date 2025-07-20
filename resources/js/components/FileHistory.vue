<template>
  <div class="w-full">
    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="files.length === 0" class="text-center py-8">
      <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No uploads yet</h3>
      <p class="text-gray-600">Upload your first CSV file to see it here</p>
    </div>

    <!-- Files List -->
    <div v-else class="space-y-4">
      <div
        v-for="file in files"
        :key="file.id"
        class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200"
      >
        <div class="flex items-center justify-between">
          <!-- File Info -->
          <div class="flex items-center space-x-3">
            <!-- File Icon -->
            <div class="flex-shrink-0">
              <svg class="h-10 w-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>

            <!-- File Details -->
            <div class="min-w-0 flex-1">
              <h4 class="text-sm font-medium text-gray-900 truncate">
                {{ file.original_filename }}
              </h4>
              <p class="text-sm text-gray-500">
                {{ formatFileSize(file.file_size) }} • {{ formatDate(file.created_at) }}
              </p>

              <!-- Real-time Status Messages -->
              <div v-if="realtimeMessages[file.id]" class="text-xs text-blue-600 mt-1">
                {{ realtimeMessages[file.id] }}
              </div>
            </div>
          </div>

          <!-- Status and Actions -->
          <div class="flex items-center space-x-4">
            <!-- Status Badge -->
            <span
              :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                getStatusBadgeClass(file.status)
              ]"
            >
              <svg
                v-if="file.status === 'processing'"
                class="-ml-0.5 mr-1.5 h-2 w-2 animate-spin"
                fill="currentColor"
                viewBox="0 0 8 8"
              >
                <circle cx="4" cy="4" r="3" stroke="currentColor" stroke-width="1" fill="none" />
              </svg>
              <div
                v-else
                :class="[
                  '-ml-0.5 mr-1.5 h-2 w-2 rounded-full',
                  getStatusDotClass(file.status)
                ]"
              ></div>
              {{ getStatusText(file.status) }}
            </span>

            <!-- Processing Duration -->
            <div v-if="file.status === 'completed' && file.processing_duration" class="text-xs text-gray-500">
              {{ file.processing_duration }}
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
              <button
                @click="deleteFile(file.id)"
                class="text-gray-400 hover:text-red-500 transition-colors"
                :disabled="file.status === 'processing'"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

      </div>

      <!-- Load More Button -->
      <div v-if="hasMore" class="text-center pt-4">
        <button
          @click="loadMore"
          :disabled="loadingMore"
          class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
        >
          <svg v-if="loadingMore" class="-ml-1 mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loadingMore ? 'Loading...' : 'Load More' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'FileHistory',
  setup() {
    const files = ref([])
    const loading = ref(true)
    const loadingMore = ref(false)
    const hasMore = ref(false)
    const currentPage = ref(1)
    const realtimeMessages = ref({})

    const loadFiles = async (page = 1, append = false) => {
      try {

        if (!append) {

          loading.value = true

        } else {

          loadingMore.value = true

        }

        const response = await axios.get(`/files/list?page=${page}`);

        if (response.data.success) {

          const newFiles = response.data.files.data;

          if (append) {

            files.value = [...files.value, ...newFiles];

          } else {

            files.value = newFiles;

          }

          hasMore.value = response.data.files.current_page < response.data.files.last_page;
          currentPage.value = response.data.files.current_page;

        }

      } catch (error) {

        console.error('Failed to load files:', error);

      } finally {

        loading.value = false;
        loadingMore.value = false;

      }
    }

    const loadMore = () => {

      loadFiles(currentPage.value + 1, true);

    }

    const deleteFile = async (fileId) => {
      if (!confirm('Are you sure you want to delete this file?')) return

      try {

        const response = await axios.delete(`/files/${fileId}`);

        if (response.data.success) {

          files.value = files.value.filter(file => file.id !== fileId);

        }

      } catch (error) {

        alert('Failed to delete file. Please try again.');

      }
    }

    const refreshFiles = () => {

      loadFiles(1, false);

    }

    const setupRealtimeUpdates = () => {

    window.addEventListener('file-uploaded', (event) => {

        files.value.unshift(event.detail)

         setupFileProgressListener(event.detail.id);
      });

      files.value.forEach(file => {

        if (file.status === 'processing' || file.status === 'pending') {

          setupFileProgressListener(file.id);

        }

      });
    }

    const setupFileProgressListener = (fileId) => {
      if (!window.Echo) return

      const channel = window.Echo.channel(`file.${fileId}`)

      channel.listen('.file.progress', (data) => {
        realtimeMessages.value[fileId] = data.message;

        const fileIndex = files.value.findIndex(f => f.id === fileId);

        if (fileIndex !== -1) {

          files.value[fileIndex].status = data.status;

        }
      });

      channel.listen('.file.completed', (data) => {
        realtimeMessages.value[fileId] = data.message;

        const fileIndex = files.value.findIndex(f => f.id === fileId);

        if (fileIndex !== -1) {

          files.value[fileIndex].status = data.status;

        }

        setTimeout(() => {

          delete realtimeMessages.value[fileId];

        }, 3000);
      });
    }

    const getStatusBadgeClass = (status) => {
      switch (status) {
        case 'pending':
          return 'bg-yellow-100 text-yellow-800';
        case 'processing':
          return 'bg-blue-100 text-blue-800';
        case 'completed':
          return 'bg-green-100 text-green-800';
        case 'failed':
          return 'bg-red-100 text-red-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    }

    const getStatusDotClass = (status) => {
      switch (status) {
        case 'pending':
          return 'bg-yellow-400';
        case 'processing':
          return 'bg-blue-400';
        case 'completed':
          return 'bg-green-400';
        case 'failed':
          return 'bg-red-400';
        default:
          return 'bg-gray-400';
      }
    }

    const getStatusText = (status) => {
      switch (status) {
        case 'pending':
          return 'Pending';
        case 'processing':
          return 'Processing';
        case 'completed':
          return 'Completed';
        case 'failed':
          return 'Failed';
        default:
          return 'Unknown';
      }
    }

    const formatFileSize = (bytes) => {

      if (bytes === 0) return '0 Bytes';

      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));

      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    const formatDate = (dateString) => {
      const date = new Date(dateString);
      const now = new Date();
      const diffMs = now - date;
      const diffMinutes = Math.floor(diffMs / 60000);
      const diffHours = Math.floor(diffMs / 3600000);
      const diffDays = Math.floor(diffMs / 86400000);

      if (diffMinutes < 1) {

        return 'Just now';

      } else if (diffMinutes < 60) {

        return `${diffMinutes}m ago`;

      } else if (diffHours < 24) {

        return `${diffHours}h ago`;

      } else if (diffDays < 7) {

        return `${diffDays}d ago`;

      } else {

        return date.toLocaleDateString();

      }
    }

    onMounted(() => {

      loadFiles();
      setupRealtimeUpdates();

    })

    return {
      files,
      loading,
      loadingMore,
      hasMore,
      realtimeMessages,
      loadMore,
      deleteFile,
      refreshFiles,
      getStatusBadgeClass,
      getStatusDotClass,
      getStatusText,
      formatFileSize,
      formatDate
    }
  }
}
</script>
