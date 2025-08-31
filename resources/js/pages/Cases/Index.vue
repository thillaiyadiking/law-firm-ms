<template>
  <AppLayout title="Cases">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cases</h2>
        <Link :href="route('cases.create')" as="button"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        New Case
        </Link>
      </div>
    </template>

    <div class="py-12">
      <Link :href="route('cases.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
      New Case
      </Link>
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6 p-6">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <input v-model="searchForm.search" @input="search" type="text" placeholder="Search cases..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <div>
              <select v-model="searchForm.status" @change="search"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Status</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="pending">Pending</option>
                <option value="closed">Closed</option>
              </select>
            </div>
            <div>
              <select v-model="searchForm.priority" @change="search"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
            <div>
              <button @click="clearFilters"
                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Clear Filters
              </button>
            </div>
          </div>
        </div>

        <!-- Cases Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case #</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date
                  </th>
                  <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="caseItem in cases.data" :key="caseItem.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ caseItem.case_number }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <Link :href="route('cases.show', caseItem.id)" class="text-blue-600 hover:text-blue-900">
                    {{ caseItem.title }}
                    </Link>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusClass(caseItem.status)"
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ formatStatus(caseItem.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getPriorityClass(caseItem.priority)"
                      class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ formatPriority(caseItem.priority) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ caseItem.assigned_to ? caseItem.assigned_to.name : 'Unassigned' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ caseItem.due_date || 'No due date' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <Link :href="route('cases.show', caseItem.id)" class="text-blue-600 hover:text-blue-900 mr-3">
                    View
                    </Link>
                    <Link :href="route('cases.edit', caseItem.id)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                    Edit
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <Pagination :links="cases.links" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Pagination from '@/components/Pagination.vue'
import { debounce } from 'lodash'

export default defineComponent({
  components: {
    AppLayout,
    Link,
    Pagination,
  },

  props: {
    cases: Object,
    filters: Object,
  },

  data() {
    return {
      searchForm: {
        search: this.filters.search || '',
        status: this.filters.status || '',
        priority: this.filters.priority || '',
      },
    }
  },

  methods: {
    search: debounce(function () {
      router.get(route('cases.index'), this.searchForm, {
        preserveState: true,
        replace: true,
      })
    }, 300),

    clearFilters() {
      this.searchForm = {
        search: '',
        status: '',
        priority: '',
      }
      this.search()
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

    getPriorityClass(priority) {
      const classes = {
        low: 'bg-green-100 text-green-800',
        medium: 'bg-yellow-100 text-yellow-800',
        high: 'bg-orange-100 text-orange-800',
        urgent: 'bg-red-100 text-red-800',
      }
      return classes[priority] || 'bg-gray-100 text-gray-800'
    },

    formatStatus(status) {
      return status.replace('_', ' ').toUpperCase()
    },

    formatPriority(priority) {
      return priority.toUpperCase()
    },
  },
})
</script>
