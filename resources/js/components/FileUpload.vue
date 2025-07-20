<template>
  <div class="w-full">
    <!-- Upload Area -->
    <div
      @drop="handleDrop"
      @dragover.prevent
      @dragenter.prevent
      @dragleave="isDragOver = false"
      @dragover="isDragOver = true"
      :class="[
        'relative border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200',
        isDragOver
          ? 'border-blue-400 bg-blue-50'
          : 'border-gray-300 hover:border-gray-400 bg-gray-50'
      ]"
    >
      <!-- Upload Icon -->
      <div class="mx-auto mb-4">
        <svg
          class="mx-auto h-12 w-12 text-gray-400"
          stroke="currentColor"
          fill="none"
          viewBox="0 0 48 48"
        >
          <path
            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
      </div>

      <!-- Upload Text -->
      <div class="text-lg font-medium text-gray-900 mb-2">
        {{ isDragOver ? 'Drop your CSV file here' : 'Upload CSV File' }}
      </div>
      <div class="text-sm text-gray-600 mb-4">
        Drag and drop your file here, or click to browse
      </div>

      <!-- File Input -->
      <input
        ref="fileInput"
        type="file"
        accept=".csv,.txt"
        @change="handleFileSelect"
        class="hidden"
      />

      <!-- Upload Button -->
      <button
        @click="$refs.fileInput.click()"
        :disabled="isUploading"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <svg v-if="!isUploading" class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <svg v-else class="-ml-1 mr-2 h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ isUploading ? 'Uploading...' : 'Choose File' }}
      </button>

      <!-- File Info -->
      <div v-if="selectedFile" class="mt-4 p-3 bg-white rounded-lg border border-gray-200">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <svg class="h-8 w-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <div>
              <div class="text-sm font-medium text-gray-900">{{ selectedFile.name }}</div>
              <div class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</div>
            </div>
          </div>
          <button
            @click="clearFile"
            class="text-gray-400 hover:text-red-500 transition-colors"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Upload Progress -->
    <div v-if="uploadProgress > 0 && uploadProgress < 100" class="mt-4">
      <div class="bg-gray-200 rounded-full h-2">
        <div
          class="bg-blue-600 h-2 rounded-full transition-all duration-300"
          :style="{ width: uploadProgress + '%' }"
        ></div>
      </div>
      <div class="text-sm text-gray-600 mt-1">Uploading... {{ uploadProgress }}%</div>
    </div>

  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'FileUpload',
  setup() {
    const isDragOver = ref(false);
    const selectedFile = ref(null);
    const isUploading = ref(false);
    const uploadProgress = ref(0);

    const handleDrop = (e) => {
      e.preventDefault();
      isDragOver.value = false;

      const files = e.dataTransfer.files;

      if (files.length > 0) {

        handleFileSelection(files[0]);

      }
    }

    const handleFileSelect = (e) => {
      const files = e.target.files;

      if (files.length > 0) {

        handleFileSelection(files[0]);

      }
    }

    const handleFileSelection = (file) => {

      const allowedTypes = ['text/csv', 'application/csv', 'text/plain'];
      const allowedExtensions = ['.csv', '.txt'];

      const hasValidType = allowedTypes.includes(file.type);
      const hasValidExtension = allowedExtensions.some(ext => file.name.toLowerCase().endsWith(ext));

      if (!hasValidType && !hasValidExtension) {
        alert('Please select a CSV file (.csv or .txt)');
        return;
      }

      if (file.size > 100 * 1024 * 1024) {

        alert('File size must be less than 100MB');
        return;

      }

      selectedFile.value = file;
      uploadFile(file);
    }

    const uploadFile = async (file) => {
      isUploading.value = true;
      uploadProgress.value = 0;

      const formData = new FormData();
      formData.append('file', file);

      try {
        const response = await axios.post('/files', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
          onUploadProgress: (progressEvent) => {
            uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total);
          }
        });

        if (response.data.success) {

          clearFile();

          window.dispatchEvent(new CustomEvent('file-uploaded', {
            detail: response.data.file
          }));

        } else {

          console.error('Upload failed:', response.data.message);

        }
      } catch (error) {

        const errorMessage = error.response?.data?.message || 'Upload failed. Please try again.';
        console.error('Upload error:', errorMessage);

      } finally {

        isUploading.value = false;
        uploadProgress.value = 0;

      }
    }

    const clearFile = () => {
      selectedFile.value = null;
      uploadProgress.value = 0;

      const fileInput = document.querySelector('input[type="file"]');

      if (fileInput) {

        fileInput.value = '';
      }

    }

    const formatFileSize = (bytes) => {
      if (bytes === 0) return '0 Bytes';

      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));

      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    onMounted(() => {
      const token = document.querySelector('meta[name="csrf-token"]');

      if (token) {

        axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');

      }
    });

    return {
      isDragOver,
      selectedFile,
      isUploading,
      uploadProgress,
      handleDrop,
      handleFileSelect,
      clearFile,
      formatFileSize
    }
  }
}
</script>
