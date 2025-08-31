<template>
  <AppLayout title="Case Details">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Case {{ caseItem.case_number }}
        </h2>
        <div class="flex space-x-3">
          <Link :href="route('cases.edit', caseItem.id)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Edit Case
          </Link>
          <Link :href="route('cases.index')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Cases
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Case Details -->
          <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
              <h3 class="text-lg font-semibold mb-4">Case Information</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Title</label>
                  <p class="text-gray-900">{{ caseItem.title }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Status</label>
                  <span :class="getStatusClass(caseItem.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ formatStatus(caseItem.status) }}
                  </span>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Priority</label>
                  <span :class="getPriorityClass(caseItem.priority)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                    {{ formatPriority(caseItem.priority) }}
                  </span>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Assigned To</label>
                  <p class="text-gray-900">{{ caseItem.assigned_to?.name || 'Unassigned' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Created By</label>
                  <p class="text-gray-900">{{ caseItem.created_by.name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Due Date</label>
                  <p class="text-gray-900">{{ caseItem.due_date || 'No due date set' }}</p>
                </div>
              </div>
              
              <div class="mt-4" v-if="caseItem.description">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <p class="text-gray-900 bg-gray-50 p-3 rounded">{{ caseItem.description }}</p>
              </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
              <h3 class="text-lg font-semibold mb-4">Notes</h3>
              
              <!-- Add Note Form -->
              <form @submit.prevent="addNote" class="mb-6">
                <textarea
                  v-model="noteForm.content"
                  rows="3"
                  placeholder="Add a note..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                ></textarea>
                <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                  Add Note
                </button>
              </form>

              <!-- Notes List -->
              <div class="space-y-4">
                <div v-for="note in caseItem.notes" :key="note.id" class="border-l-4 border-blue-500 pl-4 py-2">
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <p class="text-gray-900">{{ note.content }}</p>
                      <p class="text-sm text-gray-500 mt-1">
                        By {{ note.user.name }} on {{ formatDate(note.created_at) }}
                      </p>
                    </div>
                    <button
                      v-if="note.user.id === $page.props.auth.user.id"
                      @click="deleteNote(note.id)"
                      class="text-red-600 hover:text-red-900 text-sm"
                    >
                      Delete
                    </button>
                  </div>
                </div>
                
                <p v-if="caseItem.notes.length === 0" class="text-gray-500 italic">No notes yet.</p>
              </div>
            </div>
          </div>

          <!-- Files Section -->
          <div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
              <h3 class="text-lg font-semibold mb-4">Files</h3>
              
              <!-- File Upload -->
              <form @submit.prevent="uploadFiles" class="mb-6">
                <input
                  ref="fileInput"
                  type="file"
                  multiple
                  @change="selectFiles"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                  v-if="selectedFiles.length > 0"
                  type="submit"
                  class="mt-2 w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                >
                  Upload {{ selectedFiles.length }} file(s)
                </button>
              </form>

              <!-- Files List -->
              <div class="space-y-2">
                <div v-for="file in caseItem.files" :key="file.id" class="flex items-center justify-between p-2 bg-gray-50 rounded">
                  <div class="flex-1">
                    <p class="text-sm font-medium">{{ file.original_name }}</p>
                    <p class="text-xs text-gray-500">
                      {{ formatFileSize(file.size) }} | {{ file.uploaded_by.name }}
                    </p>
                  </div>
                  <div class="flex space-x-2">
                    <a
                      :href="route('files.download', file.id)"
                      class="text-blue-600 hover:text-blue-900 text-sm"
                    >
                      Download
                    </a>
                    <button
                      @click="deleteFile(file.id)"
                      class="text-red-600 hover:text-red-900 text-sm"
                    >
                      Delete
                    </button>
                  </div>
                </div>
                
                <p v-if="caseItem.files.length === 0" class="text-gray-500 italic text-center py-4">No files uploaded.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
export default defineComponent({
  components: {
    AppLayout,
    Link,
  },

  props: {
    caseItemProp: Object, // renamed from "case"
    users: Array,
  },

  data() {
    return {
      caseItem: this.caseItemProp,
      noteForm: useForm({
        content: '',
      }),
      selectedFiles: [],
    }
  },

  methods: {
    addNote() {
      this.noteForm.post(route('cases.notes.store', this.caseItem.id), {
        onSuccess: () => {
          this.noteForm.reset()
        },
      })
    },

    deleteNote(noteId) {
      if (confirm('Are you sure you want to delete this note?')) {
        router.delete(route('notes.destroy', noteId))
      }
    },

    selectFiles(event) {
      this.selectedFiles = Array.from(event.target.files)
    },

    uploadFiles() {
      const formData = new FormData()
      this.selectedFiles.forEach((file, index) => {
        formData.append(`files[${index}]`, file)
      })

      router.post(route('cases.files.store', this.caseItem.id), formData, {
        onSuccess: () => {
          this.selectedFiles = []
          this.$refs.fileInput.value = ''
        },
      })
    },

    deleteFile(fileId) {
      if (confirm('Are you sure you want to delete this file?')) {
        router.delete(route('files.destroy', fileId))
      }
    },

    getStatusClass(status) {
      const classes = {
        open: 'bg-blue-100 text-blue-800',
        in_progress: 'bg-yellow-100 text-yellow-800',
        pending: 'bg-orange-100 text-orange-800',
        closed: 'bg-green-100 text-green-800',
      }
      return classes[status] || 'bg-gray-100 text-gray-800'
    },

    formatStatus(status) {
      return status.replace('_', ' ').toUpperCase()
    },

    getPriorityClass(priority) {
      const classes = {
        high: 'bg-red-100 text-red-800',
        medium: 'bg-yellow-100 text-yellow-800',
        low: 'bg-green-100 text-green-800',
      }
      return classes[priority] || 'bg-gray-100 text-gray-800'
    },

    formatPriority(priority) {
      return priority.charAt(0).toUpperCase() + priority.slice(1)
    },

    formatDate(date) {
      return new Date(date).toLocaleString()
    },

    formatFileSize(size) {
      if (size < 1024) return `${size} B`
      if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`
      return `${(size / (1024 * 1024)).toFixed(1)} MB`
    },
  },
})
</script>
