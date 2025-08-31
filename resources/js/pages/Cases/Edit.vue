<template>
  <AppLayout title="Edit Case">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Case {{ caseItem.case_number }}
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Case Number (readonly) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Case Number</label>
                <input
                  :value="caseItem.case_number"
                  type="text"
                  readonly
                  class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100"
                />
              </div>

              <!-- Title -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                <input
                  v-model="form.title"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.title }"
                />
                <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">{{ form.errors.title }}</div>
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                <select
                  v-model="form.status"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.status }"
                >
                  <option value="open">Open</option>
                  <option value="in_progress">In Progress</option>
                  <option value="pending">Pending</option>
                  <option value="closed">Closed</option>
                </select>
                <div v-if="form.errors.status" class="text-red-500 text-sm mt-1">{{ form.errors.status }}</div>
              </div>

              <!-- Priority -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                <select
                  v-model="form.priority"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.priority }"
                >
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
                <div v-if="form.errors.priority" class="text-red-500 text-sm mt-1">{{ form.errors.priority }}</div>
              </div>

              <!-- Assigned To -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                <select
                  v-model="form.assigned_to"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                  <option value="">Unassigned</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
                  </option>
                </select>
              </div>

              <!-- Due Date -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                <input
                  v-model="form.due_date"
                  type="date"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.due_date }"
                />
                <div v-if="form.errors.due_date" class="text-red-500 text-sm mt-1">{{ form.errors.due_date }}</div>
              </div>

              <!-- Description -->
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea
                  v-model="form.description"
                  rows="4"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.description }"
                ></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
              </div>
            </div>

            <div class="flex justify-between items-center mt-6">
              <button
                @click="deleteCase"
                type="button"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
              >
                Delete Case
              </button>
              
              <div class="flex space-x-3">
                <Link :href="route('cases.show', caseItem.id)" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                >
                  <span v-if="form.processing">Updating...</span>
                  <span v-else>Update Case</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'

export default defineComponent({
  components: {
    AppLayout,
    Link,
  },

  props: {
    case: Object,
    users: Array,
  },

  setup(props) {
    const caseItem = props.case
    
    const form = useForm({
      title: caseItem.title,
      description: caseItem.description,
      status: caseItem.status,
      priority: caseItem.priority,
      assigned_to: caseItem.assigned_to,
      due_date: caseItem.due_date,
    })

    const submit = () => {
      form.put(route('cases.update', caseItem.id))
    }

    const deleteCase = () => {
      if (confirm('Are you sure you want to delete this case? This action cannot be undone.')) {
        router.delete(route('cases.destroy', caseItem.id))
      }
    }

    return {
      caseItem,
      form,
      submit,
      deleteCase,
    }
  },
})
</script>
